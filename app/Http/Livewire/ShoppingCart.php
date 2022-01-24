<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Gloudemans\Shoppingcart\Facades\Cart;

class ShoppingCart extends Component
{
    public $listeners = ['render'];

    public function destroy()
    {
        Cart::destroy();
        $this->emitTo('dropdown-cart', 'render');
    }

    public function delete($rowId)
    {
        Cart::remove($rowId);
        $this->emit('render');
    }

    public function render()
    {
        return view('livewire.shopping-cart');
    }
}
