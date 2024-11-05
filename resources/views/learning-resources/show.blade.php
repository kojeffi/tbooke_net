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
            <div class="row">
                <div class="col-12 col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <h1 class="h3 mb-3">{{ $resource->item_name }}</h1>
                            
                            {{-- Resource Details --}}
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Price:</strong> Ksh {{ number_format($resource->item_price, 2) }}<br>
                                    <strong>Location:</strong> {{ $resource->county }}<br>
                                    <strong>Category:</strong> {{ $resource->item_category }}<br>
                                </div>
                                <div class="col-md-6">
                                    <h4>Seller Contact Information</h4>
                                    <a href="tel:{{ $resource->contact_phone }}" class="btn btn-outline-primary me-2">
                                        <i class="fas fa-phone"></i> Call
                                    </a>
                                    <a href="https://api.whatsapp.com/send?phone={{ $resource->whatsapp_number }}" class="btn btn-outline-success me-2">
                                        <i class="fab fa-whatsapp"></i> WhatsApp
                                    </a>
                                    <a href="mailto:{{ $resource->contact_email }}" class="btn btn-outline-info">
                                        <i class="fas fa-envelope"></i> Email
                                    </a>
                                </div>
                            </div>
                            
                            {{-- Item Description --}}
                            <div class="item-description mb-3">
                                <p>{{ $resource->description }}</p>
                            </div>

                            {{-- Item Images (Gallery Format) --}}

                            @php
                            $images = json_decode($resource->item_images, true);

                            $images = is_array($images) ? $images : [];

                            $mediaCount = count($images);
                            $maxDisplayCount = 6; // Max number of images to display directly
                        @endphp
                        
                        @if ($images && $mediaCount > 0)
                            <div class="row col-md-8 g-2 mt-1">
                                @foreach ($images as $index => $image)
                                    @php
                                        // Determine the column size based on the number of images
                                        $columnSize = ($mediaCount == 1) ? 'col-md-12' : (($mediaCount == 2) ? 'col-md-6' : 'col-md-4');
                                    @endphp
                        
                                    {{-- Show the +X counter on the last image if images exceed 6 --}}
                                    @if ($index == $maxDisplayCount - 1 && $mediaCount > $maxDisplayCount)
                                        <div class="col-12 col-md-4 position-relative">
                                            <a href="{{ asset('storage/' . $image) }}" data-lightbox="gallery">
                                                <img src="{{ asset('storage/' . $image) }}" class="img-fluid mb-2" alt="item-image">    
                                                <div class="overlay-counter">
                                                    <span>+{{ $mediaCount - $maxDisplayCount }}</span>
                                                </div>
                                            </a>    
                                        </div>
                                        @break {{-- Stop loop after showing the +X image --}}
                                    @elseif ($index < $maxDisplayCount - 1)
                                        {{-- Display regular images up to the max limit --}}
                                        <div class="col-12 {{ $columnSize }}">
                                            <a href="{{ asset('storage/' . $image) }}" data-lightbox="gallery">
                                                <img src="{{ asset('storage/' . $image) }}" class="img-fluid mb-2" alt="item-image">
                                            </a>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        
                            {{-- Hidden images that are beyond the first 6, allowing Lightbox to show them when the last column is clicked --}}
                            @if ($mediaCount > $maxDisplayCount)
                                @for ($i = $maxDisplayCount; $i < $mediaCount; $i++)
                                    @php
                                        $image = $images[$i];
                                    @endphp
                                    {{-- Hidden image links to be displayed when the +X counter is clicked --}}
                                    <a href="{{ asset('storage/' . $image) }}" data-lightbox="gallery" class="d-none"></a>
                                @endfor
                            @endif
                        @endif                        
                        </div>
                    </div>
                </div>
            </div>

            {{-- Other Items Slider --}}
            <div class="row mt-5">
                <div class="col-12">
                    <h3 class="mb-4">Other Items</h3>
                    <div class="position-relative">
                        <!-- Left Arrow -->
                        <button class="btn btn-primary position-absolute" id="prevButton" style="left: 0; z-index: 10;">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <!-- Right Arrow -->
                        <button class="btn btn-primary position-absolute" id="nextButton" style="right: 0; z-index: 10;">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                        <div class="other-items-slider d-flex overflow-hidden">
                            <div class="d-flex" id="sliderContent">
                                @foreach ($otherItems as $item)
                                <div class="col-md-4 mb-4">
                                    <div class="card h-100">
                                        <!-- Link the thumbnail image to the item page -->
                                        <a href="{{ route('learning-resources.show', $item->slug) }}">
                                            <img src="{{ asset('storage/' . $item->item_thumbnail) }}" class="card-img-top" alt="Item Thumbnail" style="height: 200px; object-fit: cover;">
                                        </a>
                                        <div class="card-body">
                                            <!-- Link the item name to the item page -->
                                            <h5 class="card-title">
                                                <a href="{{ route('learning-resources.show', $item->slug) }}">{{ $item->item_name }}</a>
                                            </h5>
                                            <p class="card-text">{{ Str::limit($item->item_description, 80) }}</p>
                                            <p><strong>Price:</strong> Ksh {{ number_format($item->item_price, 2) }}</p>
                                            <p><strong>Seller:</strong>
                                                @if ($item->seller->profile_type === 'institution')
                                                    <a href="{{ auth()->id() === $item->seller->id ? route('profile.showOwn') : route('profile.show', $item->seller->username) }}">
                                                        {{ $item->seller->institutionDetails->institution_name}}
                                                    </a>
                                                @elseif ($item->seller->type === 'user')
                                                    <a href="{{ route('profile.show', $item->seller->username) }}">
                                                        {{ $item->seller->first_name }} {{ $item->seller->surname }}
                                                    </a>
                                                @else
                                                    <a href="{{ route('profile.showOwn') }}">
                                                        {{ $item->seller->first_name }} {{ $item->seller->surname }}
                                                    </a>
                                                @endif
                                            </p>
                                            <div>
                                                <a href="tel:{{ $resource->contact_phone }}" target="_blank">
                                                    <i class="fas fa-phone" style="font-size: 24px; margin-right: 15px;"></i>
                                                </a>
                                                <a href="https://wa.me/+{{ $resource->whatsapp_number }}" target="_blank">
                                                    <i class="fab fa-whatsapp" style="font-size: 24px; margin-right: 15px;"></i>
                                                </a>
                                                <a href="mailto:{{ $resource->contact_email }}" target="_blank">
                                                    <i class="fas fa-envelope" style="font-size: 24px;"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            
                            </div>
                        </div>
                    </div>
                </div>
            </div>
       </div>
    </main>
    
    {{-- footer --}}
    @include('includes.footer')
</div>

{{-- Custom CSS for gallery layout and slider --}}
<style>
    .overlay-more-images {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6);
        color: #fff;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 1.5rem;
        font-weight: bold;
        border-radius: 0.25rem;
    }

        .btn-outline-success:hover{
        color: #ffffff;
    }

    .other-items-slider {
        white-space: nowrap;
        overflow-x: scroll;
        scrollbar-width: thin;
        padding-bottom: 1rem;
    }

    .other-items-slider .col-md-4 {
        min-width: 33%;
        max-width: 33%;
        flex-shrink: 0;
        padding-right: 15px;
    }
    .other-items-slider {
        overflow: hidden; /* Prevent overflow */
    }

    #sliderContent {
        display: flex; /* Align items in a row */
        transition: transform 0.3s ease; /* Smooth transition */
    }

    .position-relative {
        position: relative; /* Relative positioning for buttons */
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sliderContent = document.getElementById('sliderContent');
        const items = sliderContent.children.length; // Total number of items
        const itemWidth = sliderContent.children[0].offsetWidth; // Width of each item
        let currentIndex = 0; // Track current index

        // Function to update the position of the slider
        function updateSliderPosition() {
            const offset = -currentIndex * itemWidth;
            sliderContent.style.transform = `translateX(${offset}px)`;
        }

        // Next button functionality
        document.getElementById('nextButton').addEventListener('click', function() {
            if (currentIndex < items - 3) { // Adjust for visible items (3 here as per col-md-4)
                currentIndex++;
                updateSliderPosition();
            }
        });

        // Previous button functionality
        document.getElementById('prevButton').addEventListener('click', function() {
            if (currentIndex > 0) {
                currentIndex--;
                updateSliderPosition();
            }
        });
    });
</script>

{{-- Add Font Awesome for icons --}}
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

