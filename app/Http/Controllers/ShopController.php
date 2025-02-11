<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $size = $request->query('size') ? $request->query('size') : 12;
        $sorting = $request->query('sorting') ? $request->query('sorting') : 'default';
        $min_price = $request->query('min') ? $request->query('min') : 1;
        $max_price = $request->query('max') ? $request->query('max') : 10000;
        $f_brands = $request->query('brands');
        $f_categories = $request->query('categories');

        if ($sorting == 'date') {
            $products = Product::whereBetween('regular_price', [$min_price, $max_price])
                ->where(function ($query) use ($f_brands) {
                    $query->whereIn('brand_id', explode(',', $f_brands))->orWhereRaw("'" . $f_brands . "' = ''");
                })
                ->where(function ($query) use ($f_categories) {
                    $query->whereIn('category_id', explode(',', $f_categories))->orWhereRaw("'" . $f_categories . "' = ''");
                })
                ->orderBy('created_at', 'DESC')->paginate($size);
        } else if ($sorting == "price") {
            $products = Product::whereBetween('regular_price', [$min_price, $max_price])
                ->where(function ($query) use ($f_brands) {
                    $query->whereIn('brand_id', explode(',', $f_brands))->orWhereRaw("'" . $f_brands . "' = ''");
                })
                ->where(function ($query) use ($f_categories) {
                    $query->whereIn('category_id', explode(',', $f_categories))->orWhereRaw("'" . $f_categories . "' = ''");
                })
                ->orderBy('regular_price', 'ASC')->paginate($size);
        } else if ($sorting == "price-desc") {
            $products = Product::whereBetween('regular_price', [$min_price, $max_price])
                ->where(function ($query) use ($f_brands) {
                    $query->whereIn('brand_id', explode(',', $f_brands))->orWhereRaw("'" . $f_brands . "' = ''");
                })
                ->where(function ($query) use ($f_categories) {
                    $query->whereIn('category_id', explode(',', $f_categories))->orWhereRaw("'" . $f_categories . "' = ''");
                })
                ->orderBy('regular_price', 'DESC')->paginate($size);
        } else {
            $products = Product::whereBetween('regular_price', [$min_price, $max_price])
                ->where(function ($query) use ($f_brands) {
                    $query->whereIn('brand_id', explode(',', $f_brands))->orWhereRaw("'" . $f_brands . "' = ''");
                })
                ->where(function ($query) use ($f_categories) {
                    $query->whereIn('category_id', explode(',', $f_categories))->orWhereRaw("'" . $f_categories . "' = ''");
                })
                ->paginate($size);
        }
        $categories = Category::orderBy("name", "ASC")->get();
        $brands = Brand::orderBy("name", "ASC")->get();
        return view('shop', compact("products", "size", "sorting", "min_price", "max_price", "categories", "brands", "f_brands", "f_categories"));
    }


    public function product_details($product_slug)
    {
        $product = Product::where("slug", $product_slug)->first();
        $rproducts = Product::where("slug", "<>", $product_slug)->get()->take(8);
        return view('details', compact("product", "rproducts"));
    }
}
