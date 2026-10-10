<?php

namespace App\Http\Controllers;

use App\Exceptions\CartException;
use App\Services\CartService;
use App\Services\CheckoutService;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(private CartService $cart)
    {
    }

    public function summary(): JsonResponse
    {
        return $this->respond(['ok' => true, 'cart' => $this->cart->summary()]);
    }

    public function add(Request $request): JsonResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer'],
            'variant_id' => ['nullable', 'integer'],
            'quantity'   => ['nullable', 'integer', 'min:1', 'max:' . CartService::MAX_PER_LINE],
        ]);

        return $this->run(fn () => $this->cart->add(
            (int) $data['product_id'],
            isset($data['variant_id']) ? (int) $data['variant_id'] : null,
            (int) ($data['quantity'] ?? 1)
        ), 'Added to your cart.');
    }

    public function update(Request $request, string $key): JsonResponse
    {
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:0', 'max:' . CartService::MAX_PER_LINE],
        ]);

        return $this->run(fn () => $this->cart->update($key, (int) $data['quantity']), 'Cart updated.');
    }

    public function remove(string $key): JsonResponse
    {
        return $this->run(fn () => $this->cart->remove($key), 'Item removed.');
    }

    public function clear(): JsonResponse
    {
        return $this->run(fn () => $this->cart->clear(), 'Cart cleared.');
    }

    private function run(Closure $action, string $message): JsonResponse
    {
        try {
            $cart = $action();
        } catch (CartException $e) {
            return $this->respond([
                'ok'       => false,
                'message'  => $e->getMessage(),
                'redirect' => $e->redirect,
                'cart'     => $this->cart->summary(), // always send the real cart back so the screen stays correct
            ], 422);
        }

        return $this->respond(['ok' => true, 'message' => $message, 'cart' => $cart]);
    }

    /**
     * The cart page sends "X-Cart-View: page" and the checkout page "X-Cart-View: checkout".
     * They get their part of the page as ready-made HTML, rendered by the same Blade partial as the page itself.
     */
    private function respond(array $body, int $status = 200): JsonResponse
    {
        $view = request()->header('X-Cart-View');

        if ($view === 'page') {
            $body['page_html'] = view('frontend.partials.cart-content', ['cart' => $body['cart']])->render();
        } elseif ($view === 'checkout') {
            $body['page_html'] = view('customer.partials.checkout-summary', [
                'cart'   => $body['cart'],
                'totals' => app(CheckoutService::class)->totals($body['cart']),
            ])->render();
        }

        return response()->json($body, $status);
    }
}
