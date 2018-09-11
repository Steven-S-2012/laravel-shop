<?php

namespace App\Http\Requests;

use App\Models\ProductSku;
use Illuminate\Foundation\Http\FormRequest;

//class AddCartRequest extends FormRequest
class AddCartRequest extends Request
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
     * @param  sku_id
     * @param  amount
     * @return array
     * closure check implement on both sku_id and amount contains 3 test.
     * Sku_id check No.2 pass 3 paras: checking attribute name, value and fail info.
     */
    public function rules()
    {
        return [
            'sku_id' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!$sku = ProductSku::find($value)) {
                        $fail('Product does not exist!');
                        return;
                    }

                    if (!$sku->product->on_sale) {
                        $fail('Product is not on sale!');
                        return;
                    }

                    if ($sku->stock ===0) {
                        $fail('Product is sold out!');
                        return;
                    }

                    if ($this->input('amount') > 0 && $sku->stock < $this->input('amount')) {
                        $fail('Product does not have enough stock!');
                        return;
                    }
                },
            ],

            'amount' => ['required', 'integer', 'min:1'],
        ];
    }

    public function attributes()
    {
        return [
            'amount' => 'Stock Amount'
        ];
    }

    public function messages()
    {
        return [
            'sku_id.required' => 'Please select a product!'
        ];
    }
}
