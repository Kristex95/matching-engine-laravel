<?php

declare(strict_types=1);

namespace Modules\Trade\Tests\Feature;

use App\Modules\Accounts\Domain\Account;
use App\Modules\Balances\PublicApi\BalanceResponseDto;
use App\Modules\Balances\PublicApi\BalancesApi;
use App\Modules\Orders\Application\Services\OrderService;
use App\Modules\Orders\Domain\Order;
use App\Modules\Trade\Application\DTO\StoreTradeDTO;
use App\Modules\Trade\Application\Services\TradeService;
use App\Modules\Trade\Domain\Trade;
use App\Modules\Trade\Infrastructure\Repositories\TradeRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Mockery;
use Mockery\MockInterface;
use RuntimeException;
use Tests\TestCase;

class TradeServiceTest extends TestCase
{
    use RefreshDatabase;

    private TradeRepository $tradeRepository;
    private OrderService|MockInterface $orderService;
    private BalancesApi|MockInterface $balanceApi;
    private TradeService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tradeRepository = app(TradeRepository::class);
        $this->orderService = Mockery::mock(OrderService::class);
        $this->balanceApi = Mockery::mock(BalancesApi::class);

        $this->service = new TradeService(
            $this->tradeRepository,
            $this->orderService,
            $this->balanceApi
        );
    }

    /**
     * Helper to quickly create an instanced BalanceResponseDto since it cannot be mocked.
     */
    private function mockBalanceResponse(): BalanceResponseDto
    {
        // Provide dummy string values to satisfy the 3 constructor arguments
        return new BalanceResponseDto('USDT', '1000.00000000', '0.00000000');
    }

    public function test_process_new_trade_maker_sell_taker_buy_creates_trade_in_database(): void
    {
        $dto = new StoreTradeDTO(
            takerOrderId: 'taker-uuid-001',
            makerOrderId: 'maker-uuid-001',
            amount: '1',
            price: '50000',
            baseCurrency: 'BTC',
            quoteCurrency: 'USDT'
        );

        $makerAccount = (new Account())->forceFill(['id' => 1]);
        $takerAccount = (new Account())->forceFill(['id' => 2]);

        $makerOrder = (new Order())->forceFill(['side' => 'sell', 'account_id' => $makerAccount->id, 'price' => '50000']);
        $takerOrder = (new Order())->forceFill(['side' => 'buy', 'account_id' => $takerAccount->id, 'price' => '50000']);

        $this->orderService->allows('getByUuidForTrading')->with('maker-uuid-001')->andReturn($makerOrder);
        $this->orderService->allows('getByUuidForTrading')->with('taker-uuid-001')->andReturn($takerOrder);

        $this->balanceApi->allows('confirmDeduction')->andReturn($this->mockBalanceResponse());
        $this->balanceApi->allows('addFunds')->andReturn($this->mockBalanceResponse());


        $result = $this->service->processNewTrade($dto);

        $this->assertInstanceOf(Trade::class, $result);
        $this->assertEquals('maker-uuid-001', $result->maker_order_id);
        $this->assertEquals('taker-uuid-001', $result->taker_order_id);
        $this->assertEquals('1', $result->amount);
        $this->assertEquals('50000', $result->price);
    }

    public function test_process_new_trade_maker_buy_taker_sell_creates_trade_in_database(): void
    {
        $dto = new StoreTradeDTO(
            takerOrderId: 'taker-uuid-002',
            makerOrderId: 'maker-uuid-002',
            amount: '2.0',
            price: '1.0',
            baseCurrency: 'XRP',
            quoteCurrency: 'USDT'
        );

        $makerAccount = (new Account())->forceFill(['id' => 10]);
        $takerAccount = (new Account())->forceFill(['id' => 20]);

        $makerOrder = (new Order())->forceFill(['side' => 'buy', 'account_id' => $makerAccount->id, 'price' => '1.0']);
        $takerOrder = (new Order())->forceFill(['side' => 'sell', 'account_id' => $takerAccount->id, 'price' => '1.0']);

        $this->orderService->allows('getByUuidForTrading')->with('maker-uuid-002')->andReturn($makerOrder);
        $this->orderService->allows('getByUuidForTrading')->with('taker-uuid-002')->andReturn($takerOrder);

        $this->balanceApi->allows('confirmDeduction')->andReturn($this->mockBalanceResponse());
        $this->balanceApi->allows('addFunds')->andReturn($this->mockBalanceResponse());


        $result = $this->service->processNewTrade($dto);

        $this->assertInstanceOf(Trade::class, $result);
        $this->assertEquals('maker-uuid-002', $result->maker_order_id);
        $this->assertEquals('taker-uuid-002', $result->taker_order_id);
        $this->assertEquals('2.0', $result->amount);
        $this->assertEquals('1.0', $result->price);
    }

    public function test_process_new_trade_calls_balance_api_for_maker_sell_taker_buy(): void
    {
        $dto = new StoreTradeDTO(
            takerOrderId: 'taker-uuid-003',
            makerOrderId: 'maker-uuid-003',
            amount: '1.5',
            price: '50000',
            baseCurrency: 'BTC',
            quoteCurrency: 'USDT'
        );

        $makerAccount = (new Account())->forceFill(['id' => 1]);
        $takerAccount = (new Account())->forceFill(['id' => 2]);

        $makerOrder = (new Order())->forceFill(['side' => 'sell', 'account_id' => $makerAccount->id, 'price' => '50000']);
        $takerOrder = (new Order())->forceFill(['side' => 'buy', 'account_id' => $takerAccount->id, 'price' => '50000']);

        $this->orderService->allows('getByUuidForTrading')->andReturnUsing(fn ($uuid) => $uuid === 'maker-uuid-003' ? $makerOrder : $takerOrder);

        $this->balanceApi->expects('confirmDeduction')
            ->with(1, 'BTC', '1.5')
            ->once()
            ->andReturn($this->mockBalanceResponse());
        $this->balanceApi->expects('addFunds')
            ->with(1, 'USDT', '75000.00000000')
            ->once()
            ->andReturn($this->mockBalanceResponse());

        $this->balanceApi->expects('confirmDeduction')
            ->with(2, 'USDT', '75000.00000000')
            ->once()
            ->andReturn($this->mockBalanceResponse());
        $this->balanceApi->expects('addFunds')
            ->with(2, 'BTC', '1.5')
            ->once()
            ->andReturn($this->mockBalanceResponse());

        $this->service->processNewTrade($dto);
    }

    public function test_process_new_trade_calls_balance_api_for_maker_buy_taker_sell(): void
    {
        $dto = new StoreTradeDTO(
            takerOrderId: 'taker-uuid-004',
            makerOrderId: 'maker-uuid-004',
            amount: '2.0',
            price: '1.0',
            baseCurrency: 'XRP',
            quoteCurrency: 'USDT'
        );

        $makerAccount = (new Account())->forceFill(['id' => 10]);
        $takerAccount = (new Account())->forceFill(['id' => 20]);

        $makerOrder = (new Order())->forceFill(['side' => 'buy', 'account_id' => $makerAccount->id, 'price' => '1.0']);
        $takerOrder = (new Order())->forceFill(['side' => 'sell', 'account_id' => $takerAccount->id, 'price' => '1.0']);

        $this->orderService->allows('getByUuidForTrading')->andReturnUsing(fn ($uuid) => $uuid === 'maker-uuid-004' ? $makerOrder : $takerOrder);

        $this->balanceApi->expects('confirmDeduction')
            ->with(10, 'USDT', '2.00000000')
            ->once()
            ->andReturn($this->mockBalanceResponse());
        $this->balanceApi->expects('addFunds')
            ->with(10, 'XRP', '2.0')
            ->once()
            ->andReturn($this->mockBalanceResponse());

        $this->balanceApi->expects('confirmDeduction')
            ->with(20, 'XRP', '2.0')
            ->once()
            ->andReturn($this->mockBalanceResponse());
        $this->balanceApi->expects('addFunds')
            ->with(20, 'USDT', '2.00000000')
            ->once()
            ->andReturn($this->mockBalanceResponse());


        $this->service->processNewTrade($dto);
    }

    public function test_process_new_trade_uses_correct_precision_for_calculations(): void
    {
        $dto = new StoreTradeDTO(
            takerOrderId: 'taker-uuid-005',
            makerOrderId: 'maker-uuid-005',
            amount: '0.123456789',
            price: '99999.99999999',
            baseCurrency: 'BTC',
            quoteCurrency: 'USD'
        );

        $makerAccount = (new Account())->forceFill(['id' => 5]);
        $takerAccount = (new Account())->forceFill(['id' => 6]);

        $makerOrder = (new Order())->forceFill(['side' => 'sell', 'account_id' => $makerAccount->id, 'price' => '99999.99999999']);
        $takerOrder = (new Order())->forceFill(['side' => 'buy', 'account_id' => $takerAccount->id, 'price' => '99999.99999999']);

        $this->orderService->allows('getByUuidForTrading')->andReturnUsing(fn ($uuid) => $uuid === 'maker-uuid-005' ? $makerOrder : $takerOrder);

        $expectedQuoteAmount = bcmul('0.123456789', '99999.99999999', 8);

        // 1. Assert Maker (ID 5) gets the precise quote amount calculated via bcmul
        $this->balanceApi->expects('addFunds')
            ->with(5, 'USD', $expectedQuoteAmount)
            ->once()
            ->andReturn($this->mockBalanceResponse());

        // 2. Allow all other required ledger calls to pass safely
        $this->balanceApi->allows('confirmDeduction')->andReturn($this->mockBalanceResponse());
        $this->balanceApi->allows('addFunds')->andReturn($this->mockBalanceResponse());

        $this->service->processNewTrade($dto);
    }

    public function test_process_new_trade_is_transactional(): void
    {
        $dto = new StoreTradeDTO(
            takerOrderId: 'taker-uuid-006',
            makerOrderId: 'maker-uuid-006',
            amount: '1.0',
            price: '50000',
            baseCurrency: 'BTC',
            quoteCurrency: 'USDT'
        );

        $makerAccount = (new Account())->forceFill(['id' => 1]);
        $takerAccount = (new Account())->forceFill(['id' => 2]);

        $makerOrder = (new Order())->forceFill(['side' => 'sell', 'account_id' => $makerAccount->id, 'price' => '50000']);
        $takerOrder = (new Order())->forceFill(['side' => 'buy', 'account_id' => $takerAccount->id, 'price' => '50000']);

        $this->orderService->allows('getByUuidForTrading')->andReturnUsing(fn ($uuid) => $uuid === 'maker-uuid-006' ? $makerOrder : $takerOrder);

        $this->balanceApi->allows('confirmDeduction')->andReturn($this->mockBalanceResponse());
        $this->balanceApi->allows('addFunds')->andReturn($this->mockBalanceResponse());

        DB::shouldReceive('transaction')
            ->once()
            ->andReturnUsing(fn ($callback) => $callback());

        $result = $this->service->processNewTrade($dto);

        $this->assertInstanceOf(Trade::class, $result);
    }

    public function test_process_new_trade_throws_exception_on_invalid_maker_side(): void
    {
        $dto = new StoreTradeDTO(
            takerOrderId: 'taker-uuid-007',
            makerOrderId: 'maker-uuid-007',
            amount: '1.0',
            price: '50000',
            baseCurrency: 'BTC',
            quoteCurrency: 'USDT'
        );

        $makerAccount = (new Account())->forceFill(['id' => 1]);
        $makerOrder = (new Order())->forceFill(['uuid' => 'maker-uuid-007', 'side' => 'invalid_side', 'account_id' => $makerAccount->id]);
        $takerOrder = (new Order())->forceFill(['uuid' => 'taker-uuid-007']);

        $this->orderService->allows('getByUuidForTrading')->andReturnUsing(fn ($uuid) => $uuid === 'maker-uuid-007' ? $makerOrder : $takerOrder);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invalid order side encountered for order maker-uuid-007: invalid_side');

        $this->service->processNewTrade($dto);
    }

    public function test_process_new_trade_throws_exception_on_invalid_taker_side(): void
    {
        $dto = new StoreTradeDTO(
            takerOrderId: 'taker-uuid-008',
            makerOrderId: 'maker-uuid-008',
            amount: '1.0',
            price: '50000',
            baseCurrency: 'BTC',
            quoteCurrency: 'USDT'
        );

        $makerAccount = (new Account())->forceFill(['id' => 1]);
        $takerAccount = (new Account())->forceFill(['id' => 2]);

        $makerOrder = (new Order())->forceFill(['side' => 'sell', 'account_id' => $makerAccount->id, 'price' => '50000']);
        $takerOrder = (new Order())->forceFill(['uuid' => 'taker-uuid-008', 'side' => 'invalid_side', 'account_id' => $takerAccount->id, 'price' => '50000']);

        $this->orderService->allows('getByUuidForTrading')->andReturnUsing(fn ($uuid) => $uuid === 'maker-uuid-008' ? $makerOrder : $takerOrder);

        $this->balanceApi->allows('confirmDeduction')->andReturn($this->mockBalanceResponse());
        $this->balanceApi->allows('addFunds')->andReturn($this->mockBalanceResponse());


        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invalid order side encountered for order taker-uuid-008: invalid_side');


        $this->service->processNewTrade($dto);
    }

    public function test_process_new_trade_different_currency_pairs(): void
    {
        $currencyPairs = [
            ['base' => 'ETH', 'quote' => 'USDT', 'amount' => '10.5'],
            ['base' => 'USDT', 'quote' => 'USD', 'amount' => '1000'],
            ['base' => 'BNB', 'quote' => 'EUR', 'amount' => '5'],
        ];

        foreach ($currencyPairs as $pair) {

            $dto = new StoreTradeDTO(
                takerOrderId: 'taker-' . $pair['base'],
                makerOrderId: 'maker-' . $pair['base'],
                amount: $pair['amount'],
                price: '100',
                baseCurrency: $pair['base'],
                quoteCurrency: $pair['quote']
            );

            $makerAccount = (new Account())->forceFill(['id' => 1]);
            $takerAccount = (new Account())->forceFill(['id' => 2]);

            $makerOrder = (new Order())->forceFill(['side' => 'sell', 'account_id' => $makerAccount->id, 'price' => '100']);
            $takerOrder = (new Order())->forceFill(['side' => 'buy', 'account_id' => $takerAccount->id, 'price' => '100']);

            $this->orderService->allows('getByUuidForTrading')->andReturnUsing(fn ($uuid) => str_contains($uuid, 'maker-') ? $makerOrder : $takerOrder);

            $this->balanceApi->allows('confirmDeduction')->andReturn($this->mockBalanceResponse());
            $this->balanceApi->allows('addFunds')->andReturn($this->mockBalanceResponse());

            $result = $this->service->processNewTrade($dto);

            $this->assertInstanceOf(Trade::class, $result);
            $this->assertEquals($pair['base'], $dto->baseCurrency);
            $this->assertEquals($pair['quote'], $dto->quoteCurrency);
        }
    }
}
