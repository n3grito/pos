<?php

namespace App\Livewire;

use App\Models\CashRegisterSession;
use App\Models\Client;
use App\Models\PriceList;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Service;
use App\Models\Branch;
use App\Models\InventoryMovement;
use App\Models\Promotion;
use App\Services\LoyaltyService;
use App\Services\PromotionService;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class QuickSale extends Component
{
    public bool $show = false;

    public string $search = '';

    public string $barcode = '';

    public array $cart = [];

    public string $paymentMethod = 'cash';

    public ?float $amountPaid = null;

    public ?int $clientId = null;

    public ?int $priceListId = null;

    public ?int $cashRegisterSessionId = null;

    public string $clientName = '';

    public string $clientNit = '';

    public string $paymentReference = '';

    public ?float $total = 0;

    public ?float $change = 0;

    public string $statusMessage = '';

    public int $pointsToRedeem = 0;

    public float $pointsDiscount = 0;

    public int $earnedPoints = 0;

    public ?int $appliedPromotionId = null;

    public string $appliedPromotionName = '';

    public float $promotionDiscount = 0;

    protected $listeners = ['openQuickSale' => 'open'];

    public function open(): void
    {
        $this->resetExcept('show');
        $this->show = true;
    }

    public function close(): void
    {
        $this->reset();
    }

    public function addByBarcode(): void
    {
        $product = Product::where('barcode', $this->barcode)
            ->where('is_active', true)
            ->where('available_for_sale', true)
            ->first();

        if (!$product) {
            $this->statusMessage = __('Producto no encontrado con código: :code', ['code' => $this->barcode]);
            return;
        }

        $this->addProduct($product);
        $this->barcode = '';
        $this->statusMessage = '';
    }

    public function selectProduct(int $productId): void
    {
        $product = Product::find($productId);

        if (!$product || !$product->is_active || !$product->available_for_sale) {
            return;
        }

        $this->addProduct($product);
        $this->search = '';
        $this->statusMessage = '';
    }

    protected function addProduct(Product $product): void
    {
        if ($product->stock <= 0) {
            $this->statusMessage = __(':name no tiene stock disponible', ['name' => $product->name]);
            return;
        }

        $existingKey = null;
        foreach ($this->cart as $key => $item) {
            if ($item['product_id'] === $product->id) {
                $existingKey = $key;
                break;
            }
        }

        if ($existingKey !== null) {
            if ($this->cart[$existingKey]['quantity'] < $product->stock) {
                $this->cart[$existingKey]['quantity']++;
                $this->cart[$existingKey]['subtotal'] = round($this->cart[$existingKey]['quantity'] * $this->cart[$existingKey]['price'], 2);
            } else {
                $this->statusMessage = __('Stock máximo alcanzado para :name', ['name' => $product->name]);
            }
        } else {
            $price = $product->selling_price;

            if ($this->priceListId) {
                $priceList = PriceList::find($this->priceListId);
                if ($priceList) {
                    $price = $product->getPriceForList($priceList);
                }
            }

            $this->cart[] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'price' => (float) $price,
                'quantity' => 1,
                'stock' => (float) $product->stock,
                'subtotal' => (float) $price,
                'tax_percentage' => (float) $product->tax_percentage,
            ];
        }

        $this->calculateTotal();
    }

    public function updateQuantity(int $index, float $quantity): void
    {
        if (!isset($this->cart[$index])) return;

        $item = &$this->cart[$index];
        $quantity = max(0.001, min($quantity, $item['stock']));
        $item['quantity'] = $quantity;
        $item['subtotal'] = round($quantity * $item['price'], 2);

        if ($quantity <= 0) {
            array_splice($this->cart, $index, 1);
        }

        $this->calculateTotal();
    }

    public function removeItem(int $index): void
    {
        if (isset($this->cart[$index])) {
            array_splice($this->cart, $index, 1);
            $this->calculateTotal();
        }
    }

    public function calculateTotal(): void
    {
        $subtotal = round(array_sum(array_column($this->cart, 'subtotal')), 2);

        $this->updatePromotion($subtotal);

        $totalAfterPromotion = $subtotal - $this->promotionDiscount;
        $totalAfterPoints = max(0, $totalAfterPromotion - $this->pointsDiscount);

        $this->total = round($totalAfterPoints, 2);

        if ($this->paymentMethod === 'cash' && $this->amountPaid !== null) {
            $this->change = round($this->amountPaid - $this->total, 2);
        }
    }

    public function updatePromotion(float $subtotal): void
    {
        $totalQuantity = collect($this->cart)->sum('quantity');

        $promotionService = app(PromotionService::class);
        $best = $promotionService->findBestPromotion(
            $subtotal,
            $this->cart,
            $this->clientId
        );

        if ($best) {
            $this->appliedPromotionId = $best->id;
            $this->appliedPromotionName = $best->name;
            $this->promotionDiscount = $best->calculateDiscount($subtotal);
        } else {
            $this->appliedPromotionId = null;
            $this->appliedPromotionName = '';
            $this->promotionDiscount = 0;
        }
    }

    public function updatedClientId(): void
    {
        $this->pointsToRedeem = 0;
        $this->pointsDiscount = 0;
        $this->calculateTotal();
    }

    public function updatedPointsToRedeem(): void
    {
        if (!$this->clientId) {
            $this->pointsToRedeem = 0;
            $this->pointsDiscount = 0;
            return;
        }

        $client = Client::find($this->clientId);
        if (!$client) {
            $this->pointsToRedeem = 0;
            $this->pointsDiscount = 0;
            return;
        }

        $this->pointsToRedeem = min($this->pointsToRedeem, $client->points);
        $this->pointsDiscount = (new LoyaltyService())->calculateDiscountValue($this->pointsToRedeem);
        $this->calculateTotal();
    }

    public function updatedAmountPaid(): void
    {
        $this->calculateTotal();
    }

    public function updatedPaymentMethod(): void
    {
        $this->calculateTotal();
    }

    public function checkout(): void
    {
        if (empty($this->cart)) {
            $this->statusMessage = __('Agregue al menos un producto al carrito');
            return;
        }

        if ($this->paymentMethod === 'cash' && $this->amountPaid !== null && $this->amountPaid < $this->total) {
            $this->statusMessage = __('El monto recibido es menor que el total');
            return;
        }

        if (in_array($this->paymentMethod, ['card', 'transfer'])) {
            if (empty($this->clientName) || empty($this->clientNit) || empty($this->paymentReference)) {
                $this->statusMessage = __('Complete los datos del cliente y referencia de pago');
                return;
            }
        }

        try {
            DB::transaction(function () {
                $count = Sale::whereDate('created_at', today())->count();
                $invoiceNumber = 'INV-' . date('Ymd') . '-' . str_pad($count + 1, 5, '0', STR_PAD_LEFT);

                $subtotal = 0;
                $totalTax = 0;

                $user = auth()->user();
                $branchId = $user->branch_id;
                if (!$branchId && $this->cashRegisterSessionId) {
                    $session = CashRegisterSession::with('cashRegister')->find($this->cashRegisterSessionId);
                    $branchId = $session?->cashRegister?->branch_id;
                }
                if (!$branchId) {
                    $branchId = Branch::where('is_active', true)->value('id');
                }
                if (!$branchId) {
                    throw new \RuntimeException('No hay una sucursal activa configurada.');
                }

                $sale = Sale::create([
                    'invoice_number' => $invoiceNumber,
                    'user_id' => auth()->id(),
                    'client_id' => $this->clientId ?: null,
                    'branch_id' => $branchId,
                    'warehouse_id' => $user->warehouse_id,
                    'cash_register_session_id' => $this->cashRegisterSessionId ?: null,
                    'price_list_id' => $this->priceListId ?: null,
                    'discount_type' => $this->appliedPromotionId ? 'promotion' : ($this->pointsToRedeem > 0 ? 'points' : null),
                    'discount_value' => $this->appliedPromotionId ? $this->promotionDiscount : $this->pointsDiscount,
                    'discount_amount' => $this->promotionDiscount + $this->pointsDiscount,
                    'promotion_id' => $this->appliedPromotionId,
                    'points_redeemed' => $this->pointsToRedeem,
                    'subtotal' => 0,
                    'tax' => 0,
                    'total' => 0,
                    'amount_paid' => $this->paymentMethod === 'cash' ? $this->amountPaid : null,
                    'change' => $this->paymentMethod === 'cash' && $this->amountPaid ? round($this->amountPaid - $this->total, 2) : null,
                    'payment_reference' => in_array($this->paymentMethod, ['card', 'transfer']) ? $this->paymentReference : null,
                    'client_name' => in_array($this->paymentMethod, ['card', 'transfer']) ? $this->clientName : null,
                    'client_nit' => in_array($this->paymentMethod, ['card', 'transfer']) ? $this->clientNit : null,
                    'payment_method' => $this->paymentMethod,
                    'status' => 'completed',
                    'date' => today(),
                ]);

                foreach ($this->cart as $item) {
                    $product = Product::lockForUpdate()->findOrFail($item['product_id']);

                    if ($product->stock < $item['quantity']) {
                        throw ValidationException::withMessages([
                            'cart' => __('Stock insuficiente para :name. Disponible: :stock', [
                                'name' => $product->name,
                                'stock' => $product->stock,
                            ]),
                        ]);
                    }

                    $lineSubtotal = $item['quantity'] * $item['price'];
                    $lineTax = round($lineSubtotal * ($item['tax_percentage'] / 100), 2);
                    $subtotal += $lineSubtotal;
                    $totalTax += $lineTax;

                    SaleDetail::create([
                        'sale_id' => $sale->id,
                        'product_id' => $product->id,
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        'subtotal' => $lineSubtotal,
                    ]);

                    $product->decrement('stock', $item['quantity']);

                    InventoryMovement::create([
                        'product_id' => $product->id,
                        'warehouse_id' => $sale->warehouse_id,
                        'user_id' => auth()->id(),
                        'type' => 'out',
                        'quantity' => $item['quantity'],
                        'reference' => $sale->invoice_number,
                        'notes' => 'Venta rápida #' . $sale->invoice_number,
                    ]);
                }

                $totalAmount = $subtotal + $totalTax;
                $discountAmount = $this->promotionDiscount + $this->pointsDiscount;
                $finalTotal = round(max(0, $totalAmount - $discountAmount), 2);

                $pointsEarned = 0;
                if ($this->clientId) {
                    $client = Client::find($this->clientId);

                    if ($client && $this->pointsToRedeem > 0) {
                        (new LoyaltyService())->redeemPoints(
                            $client,
                            $this->pointsToRedeem,
                            'sale',
                            $sale->id,
                            'Canje en venta #' . $sale->invoice_number
                        );
                    }

                    if ($client) {
                        $pointsEarned = (new LoyaltyService())->earnPoints(
                            $client,
                            $finalTotal,
                            'sale',
                            $sale->id,
                            'Venta #' . $sale->invoice_number
                        );
                    }
                }

                $sale->update([
                    'subtotal' => $subtotal,
                    'tax' => $totalTax,
                    'total' => $finalTotal,
                    'points_earned' => $pointsEarned,
                    'amount_paid' => $this->paymentMethod === 'cash' ? $this->amountPaid : null,
                    'change' => $this->paymentMethod === 'cash' && $this->amountPaid ? round($this->amountPaid - $finalTotal, 2) : null,
                ]);

                $this->dispatch('sale-completed', saleId: $sale->id, invoiceNumber: $sale->invoice_number);
                $this->dispatch('notify', message: 'Venta #' . $sale->invoice_number . ' completada', type: 'success');

                $this->resetExcept('show');
                $this->show = false;
            });
        } catch (ValidationException $e) {
            $this->statusMessage = $e->getMessage();
        } catch (\Exception $e) {
            $this->statusMessage = 'Error: ' . $e->getMessage();
        }
    }

    public function render()
    {
        $products = collect();
        $clients = Client::where('is_active', true)->get();
        $sessions = CashRegisterSession::with('cashRegister')->where('status', 'open')->get();
        $priceLists = PriceList::all();

        if (strlen($this->search) >= 1) {
            $query = '%' . $this->search . '%';
            $products = Product::with('category')
                ->where('is_active', true)
                ->where('available_for_sale', true)
                ->where(function ($q) use ($query) {
                    $q->where('name', 'like', $query)
                      ->orWhere('sku', 'like', $query)
                      ->orWhere('barcode', 'like', $query);
                })
                ->limit(8)
                ->get();
        }

        return view('livewire.quick-sale', [
            'products' => $products,
            'clients' => $clients,
            'sessions' => $sessions,
            'priceLists' => $priceLists,
        ]);
    }

    public function getCheckoutData(): array
    {
        return [
            'cart' => array_map(fn($item) => [
                'product_id' => $item['product_id'] ?? $item['productId'] ?? null,
                'quantity' => $item['quantity'] ?? 1,
                'price' => $item['price'] ?? 0,
            ], $this->cart),
            'paymentMethod' => $this->paymentMethod,
            'amountPaid' => $this->amountPaid,
            'clientName' => $this->clientName,
            'clientNit' => $this->clientNit,
            'paymentReference' => $this->paymentReference,
            'clientId' => $this->clientId,
        ];
    }

    public function resetCart(): void
    {
        $this->cart = [];
        $this->search = '';
        $this->barcode = '';
        $this->paymentMethod = 'cash';
        $this->amountPaid = null;
        $this->clientName = '';
        $this->clientNit = '';
        $this->paymentReference = '';
        $this->clientId = null;
        $this->total = 0;
        $this->change = 0;
        $this->statusMessage = '';
    }
}
