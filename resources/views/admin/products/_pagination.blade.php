@if(method_exists($products, 'links'))
    <div class="pagination-wrapper">
        {{ $products->withQueryString()->links() }}
    </div>
@endif
