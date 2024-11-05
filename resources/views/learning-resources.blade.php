{{-- resources.blade.php --}}
@include('includes.header')
{{-- Sidebar --}}
@include('includes.sidebar')
@include('sweetalert::alert')

{{-- Topbar --}}
<div class="main">
    @include('includes.topbar')
    {{-- Main Content --}}
    <main class="content">
       <div class="container-fluid p-0">
            <div>
                <div class="row content">
                   <div class="row">
                        <div class="col-md-5">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h1 class="h3">Tbooke Shop</h1>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="d-md-block">
                                <div class="text-end">
                                    <a href="{{ route('learning-resources.create') }}" class="btn btn-primary">Sell on Tbooke</a>
                                    @if(auth()->check())
                                        <a href="{{ route('learning-resources.user', auth()->user()->username) }}" class="btn btn-info my-conent">My Resources</a>
                                    @endif
                                 </div>
                            </div>
                        </div>
                   </div>
                    
                    {{-- Unified Search Input --}}
                    <div class="col-md-12 mb-4">
                        <div class="row g-3 align-items-center">
                            <div class="col-md-12">
                                <label for="searchInput" class="form-label">Search Items</label>
                                <input type="text" class="form-control" id="searchInput" placeholder="Search by item name, seller name, or price">
                            </div>
                        </div>
                    </div>
                    
                    {{-- Display Resources --}}
                    @foreach ($resources as $resource)
                        <div class="col-12 col-md-3 resource-item" 
                             data-item-name="{{ strtolower($resource->item_name) }}"
                             data-seller-full-name="{{ strtolower($resource->seller->first_name . ' ' . $resource->seller->surname) }}"
                             data-item-price="{{ strtolower($resource->item_price) }}">
                            <div class="card">
                                <a href="{{ route('learning-resources.show', ['slug' => $resource->slug]) }}">
                                    <img class="card-img-top resource-img" src="{{ $resource->item_thumbnail ? asset('storage/' . $resource->item_thumbnail) : asset('default-images/default-bg.jpg') }} " alt="{{ $resource->item_name }}">
                                </a>    
                                    <div class="card-header seller">
                                        <h5 class="card-title seller-name">Seller: 
                                            @if ($resource->user->profile_type == 'institution')
                                                <a href="{{auth()->id() === $resource->user->id ? route('profile.showOwn') : route('profile.show', $resource->user->username) }}">
                                                    {{ $resource->user->institutionDetails->institution_name }}
                                                </a>
                                            @else
                                                <a href="{{ auth()->id() === $resource->user->id ? route('profile.showOwn') : route('profile.show', $resource->user->username) }}">
                                                    {{ $resource->seller->first_name }} {{ $resource->seller->surname }}
                                                </a>
                                            @endif
                                        </h5>
                                        <h5 class="card-title resource-title"><a href="{{ route('learning-resources.show', ['slug' => $resource->slug]) }}">{{ $resource->item_name }}</a></h5>
                                        <p class="resource-price">Price: KES {{ number_format($resource->item_price, 0, '.', ',') }}</p>
                                    </div>
                                <div style="padding-top: 0" class="card-body">
                                    <p class="card-text">{{ Str::limit($resource->description, 18) }}</p>
                                    <a href="#" class="card-link learning-resources-button">Contact Seller</a>
                                    <div class="seller-contact">
                                        <a href="tel:{{ $resource->contact_phone }}" target="_blank"><i class="fas fa-phone" style="font-size: 24px; margin-right: 15px;"></i></a>
                                        <a href="https://wa.me/+{{ $resource->whatsapp_number }}" target="_blank"><i class="fab fa-whatsapp" style="font-size: 24px; margin-right: 15px;"></i></a>
                                        <a href="mailto:{{ $resource->contact_email }}" target="_blank"><i class="fas fa-envelope" style="font-size: 24px;"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
       </div>
    </main>
    {{-- footer --}}
    @include('includes.footer')
</div>


<!-- JavaScript for Search Filtering -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('searchInput');
        const resourceItems = document.querySelectorAll('.resource-item');

        searchInput.addEventListener('input', function () {
            const query = searchInput.value.toLowerCase();

            resourceItems.forEach(function (item) {
                const itemName = item.getAttribute('data-item-name').toLowerCase();
                const sellerFullName = item.getAttribute('data-seller-full-name').toLowerCase();
                const price = item.getAttribute('data-item-price').toLowerCase();

                if (itemName.includes(query)  || sellerFullName.includes(query) || price.includes(query)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });
</script>
