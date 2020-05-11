<?php

namespace App\Http\Requests;

use App\Models\ProductSku;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

//class OrderRequest extends FormRequest
class OrderRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //check submitted ID exist and belong to user
            //to prevent that user traverse each address & id
            'address_id'     => ['required', Rule::exists('user_addresses', 'id')->where('user_id', $this->user()->id)],
            'items'          => ['required', 'array'],
            'items.*.sku_id' => [ //check sku_id in each subarray of items array
                'required',
                function ($attribute, $value, $fail) {
                    if (!$sku = ProductSku::find($value)) {
                        $fail('Product does not exist!');
                        return;
                    }
                    if (!$sku->product->on_sale) {
                        $fail('Product is unavailable!');
                        return;
                    }
                    if ($sku->stock === 0) {
                        $fail('Product sold out!');
                        return;
                    }

                    //get current index
                    preg_match('/items\.(\d+)\.sku_id/', $attribute, $m);
                    $index  = $m[1];

                    //find amount attribute according index
                    $amount = $this->input('items')[$index]['amount'];
                    if ($amount > 0 && $amount > $sku->stock) {
                        $fail('Out of stock!');
                        return;
                    }
                },
            ],
            'items.*.amount' => ['required', 'integer', 'min:1'],
        ];
    }
}
