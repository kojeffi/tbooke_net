@include('includes.header')
{{-- Sidebar --}}
@include('includes.sidebar')

{{-- Topbar --}}
<div class="main">
    @include('includes.topbar')
    {{-- Main Content --}}
    <main class="content">
        <div class="container-fluid p-0">
            <div class="row justify-content-around">
                <div class="col-md-10 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Sell on Tbooke</h5>
                        </div>

                        <div class="row card-body content-creation-form">
                            <form method="POST" action="{{ route('learning-resources.store') }}" enctype="multipart/form-data">
                                @csrf
                                @method('post')
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <input type="text" class="form-control" name="item_name" placeholder="Item Name" value="{{ old('item_name') }}">
                                            @if($errors->has('item_name'))
                                                <div class="text-danger">{{ $errors->first('item_name') }}</div>
                                            @endif
                                        </div>
                                        <div class="mb-3">
                                            <select class="form-select rounded" id="resourceCategory" name="item_category">
                                                <option selected="" disabled="">Select category</option>
                                                <option value="Books" {{ old('item_category') == 'Books' ? 'selected' : '' }}>Books</option>
                                                <option value="Stationery" {{ old('item_category') == 'Stationery' ? 'selected' : '' }}>Stationery</option>
                                                <option value="Educational Resources" {{ old('item_category') == 'Educational Resources' ? 'selected' : '' }}>Educational Resources</option>
                                                <option value="Educational Software" {{ old('item_category') == 'Educational Software' ? 'selected' : '' }}>Educational Software</option>
                                                <option value="Electronics" {{ old('item_category') == 'Electronics' ? 'selected' : '' }}>Electronics</option>
                                                <option value="Online Courses and Tutorials" {{ old('item_category') == 'Online Courses and Tutorials' ? 'selected' : '' }}>Online Courses and Tutorials</option>
                                                <option value="Sporting Equipment" {{ old('item_category') == 'Sporting Equipment' ? 'selected' : '' }}>Sporting Equipment</option>
                                                <option value="Other" {{ old('item_category') == 'Other' ? 'selected' : '' }}>Other</option>
                                            </select>
                                            @if($errors->has('item_category'))
                                                <div class="text-danger">{{ $errors->first('item_category') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <select class="form-select rounded" id="county" name="county">
                                                <option selected="" disabled="">Select county</option>
                                                <option value="Baringo" {{ old('county') == 'Baringo' ? 'selected' : '' }}>Baringo</option>
                                                <option value="Bomet" {{ old('county') == 'Bomet' ? 'selected' : '' }}>Bomet</option>
                                                <option value="Bungoma" {{ old('county') == 'Bungoma' ? 'selected' : '' }}>Bungoma</option>
                                                <option value="Busia" {{ old('county') == 'Busia' ? 'selected' : '' }}>Busia</option>
                                                <option value="Elgeyo Marakwet" {{ old('county') == 'Elgeyo Marakwet' ? 'selected' : '' }}>Elgeyo Marakwet</option>
                                                <option value="Embu" {{ old('county') == 'Embu' ? 'selected' : '' }}>Embu</option>
                                                <option value="Garissa" {{ old('county') == 'Garissa' ? 'selected' : '' }}>Garissa</option>
                                                <option value="Homa Bay" {{ old('county') == 'Homa Bay' ? 'selected' : '' }}>Homa Bay</option>
                                                <option value="Isiolo" {{ old('county') == 'Isiolo' ? 'selected' : '' }}>Isiolo</option>
                                                <option value="Kajiado" {{ old('county') == 'Kajiado' ? 'selected' : '' }}>Kajiado</option>
                                                <option value="Kakamega" {{ old('county') == 'Kakamega' ? 'selected' : '' }}>Kakamega</option>
                                                <option value="Kericho" {{ old('county') == 'Kericho' ? 'selected' : '' }}>Kericho</option>
                                                <option value="Kiambu" {{ old('county') == 'Kiambu' ? 'selected' : '' }}>Kiambu</option>
                                                <option value="Kilifi" {{ old('county') == 'Kilifi' ? 'selected' : '' }}>Kilifi</option>
                                                <option value="Kirinyaga" {{ old('county') == 'Kirinyaga' ? 'selected' : '' }}>Kirinyaga</option>
                                                <option value="Kisii" {{ old('county') == 'Kisii' ? 'selected' : '' }}>Kisii</option>
                                                <option value="Kisumu" {{ old('county') == 'Kisumu' ? 'selected' : '' }}>Kisumu</option>
                                                <option value="Kitui" {{ old('county') == 'Kitui' ? 'selected' : '' }}>Kitui</option>
                                                <option value="Kwale" {{ old('county') == 'Kwale' ? 'selected' : '' }}>Kwale</option>
                                                <option value="Laikipia" {{ old('county') == 'Laikipia' ? 'selected' : '' }}>Laikipia</option>
                                                <option value="Lamu" {{ old('county') == 'Lamu' ? 'selected' : '' }}>Lamu</option>
                                                <option value="Machakos" {{ old('county') == 'Machakos' ? 'selected' : '' }}>Machakos</option>
                                                <option value="Makueni" {{ old('county') == 'Makueni' ? 'selected' : '' }}>Makueni</option>
                                                <option value="Mandera" {{ old('county') == 'Mandera' ? 'selected' : '' }}>Mandera</option>
                                                <option value="Marsabit" {{ old('county') == 'Marsabit' ? 'selected' : '' }}>Marsabit</option>
                                                <option value="Meru" {{ old('county') == 'Meru' ? 'selected' : '' }}>Meru</option>
                                                <option value="Migori" {{ old('county') == 'Migori' ? 'selected' : '' }}>Migori</option>
                                                <option value="Mombasa" {{ old('county') == 'Mombasa' ? 'selected' : '' }}>Mombasa</option>
                                                <option value="Murang'a" {{ old('county') == 'Muranga' ? 'selected' : '' }}>Murang'a</option>
                                                <option value="Nairobi" {{ old('county') == 'Nairobi' ? 'selected' : '' }}>Nairobi</option>
                                                <option value="Nakuru" {{ old('county') == 'Nakuru' ? 'selected' : '' }}>Nakuru</option>
                                                <option value="Nandi" {{ old('county') == 'Nandi' ? 'selected' : '' }}>Nandi</option>
                                                <option value="Narok" {{ old('county') == 'Narok' ? 'selected' : '' }}>Narok</option>
                                                <option value="Nyamira" {{ old('county') == 'Nyamira' ? 'selected' : '' }}>Nyamira</option>
                                                <option value="Nyandarua" {{ old('county') == 'Nyandarua' ? 'selected' : '' }}>Nyandarua</option>
                                                <option value="Nyeri" {{ old('county') == 'Nyeri' ? 'selected' : '' }}>Nyeri</option>
                                                <option value="Samburu" {{ old('county') == 'Samburu' ? 'selected' : '' }}>Samburu</option>
                                                <option value="Siaya" {{ old('county') == 'Siaya' ? 'selected' : '' }}>Siaya</option>
                                                <option value="Taita Taveta" {{ old('county') == 'Taita Taveta' ? 'selected' : '' }}>Taita Taveta</option>
                                                <option value="Tana River" {{ old('county') == 'Tana River' ? 'selected' : '' }}>Tana River</option>
                                                <option value="Tharaka Nithi" {{ old('county') == 'Tharaka Nithi' ? 'selected' : '' }}>Tharaka Nithi</option>
                                                <option value="Trans Nzoia" {{ old('county') == 'Trans Nzoia' ? 'selected' : '' }}>Trans Nzoia</option>
                                                <option value="Turkana" {{ old('county') == 'Turkana' ? 'selected' : '' }}>Turkana</option>
                                                <option value="Uasin Gishu" {{ old('county') == 'Uasin Gishu' ? 'selected' : '' }}>Uasin Gishu</option>
                                                <option value="Vihiga" {{ old('county') == 'Vihiga' ? 'selected' : '' }}>Vihiga</option>
                                                <option value="Wajir" {{ old('county') == 'Wajir' ? 'selected' : '' }}>Wajir</option>
                                                <option value="West Pokot" {{ old('county') == 'West Pokot' ? 'selected' : '' }}>West Pokot</option>
                                            </select>
                                            @if($errors->has('county'))
                                                <div class="text-danger">{{ $errors->first('county') }}</div>
                                            @endif
                                        </div>
                                        <div class="mb-3">
                                            <input type="number" class="form-control" name="item_price" placeholder="Item Price" value="{{ old('item_price') }}">
                                            @if($errors->has('item_price'))
                                                <div class="text-danger">{{ $errors->first('item_price') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <input type="number" class="form-control" name="whatsapp_number" placeholder="Whatsapp Number starting with 254" value="{{ old('whatsapp_number') }}">
                                            @if($errors->has('whatsapp_number'))
                                                <div class="text-danger">{{ $errors->first('whatsapp_number') }}</div>
                                            @endif
                                        </div>
                                        <div class="mb-3">
                                            <input type="text" class="form-control" name="contact_email" placeholder="Contact Email" value="{{ old('contact_email') }}">
                                            @if($errors->has('contact_email'))
                                                <div class="text-danger">{{ $errors->first('contact_email') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <input type="number" class="form-control" name="contact_phone" placeholder="Contact Number" value="{{ old('contact_phone') }}">
                                            @if($errors->has('contact_phone'))
                                                <div class="text-danger">{{ $errors->first('contact_phone') }}</div>
                                            @endif
                                        </div>
                                        <div class="mb-3">
                                            <label for="item_thumbnail" class="form-label">Item Thumbnail (Optional)</label>
                                            <input name="item_thumbnail" type="file" class="form-control mb-3">
                                            @if($errors->has('item_thumbnail'))
                                                <div class="text-danger">{{ $errors->first('item_thumbnail') }}</div>
                                            @endif
                                        </div>
                                    </div>
                            
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="item_images" class="form-label">Item Images</label>
                                            <input type="file" id="item_images" class="form-control" name="item_images[]" accept="image/*" multiple onchange="previewImages(event)">
                                            @if($errors->has('item_images'))
                                                <div class="text-danger">{{ $errors->first('item_images') }}</div>
                                            @endif
                                        </div>
                                        <div id="image_previews" class="mb-3 d-flex flex-wrap"></div>
                                    </div>
                                                             
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <textarea class="form-control" name="description" placeholder="Start typing item description..." rows="5">{{ old('description') }}</textarea>
                                            @if($errors->has('description'))
                                                <div class="text-danger">{{ $errors->first('description') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <input type="submit" class="btn btn-primary" value="Submit" />
                                    </div>
                                </div>
                            </form>                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    {{-- footer --}}
    @include('includes.footer')
</div>


<script>

let selectedFiles = []; // Array to track selected files

function previewImages(event) {
    const previewContainer = document.getElementById('image_previews');
    previewContainer.innerHTML = ''; // Clear previous previews
    selectedFiles = Array.from(event.target.files); // Store selected files

    selectedFiles.forEach((file, index) => {
        const reader = new FileReader();

        reader.onload = (e) => {
            const imagePreview = document.createElement('div');
            imagePreview.className = 'position-relative me-2'; // Add margin for spacing

        // Inside the forEach loop where you create the image preview
        imagePreview.innerHTML = `
            <img src="${e.target.result}" alt="Image Preview" class="img-thumbnail" style="width: 100px; height: 100px;">
            <button type="button" class="btn-close position-absolute top-0 end-0 close-button-preview" onclick="removeImage(${index})" aria-label="Close"></button>
        `;


            previewContainer.appendChild(imagePreview);
        };

        reader.readAsDataURL(file);
    });
}

// Function to remove an image from preview and input
function removeImage(index) {
    // Remove the file from the selectedFiles array
    selectedFiles.splice(index, 1);

    // Clear the input and update with remaining files
    const input = document.getElementById('item_images');
    const dataTransfer = new DataTransfer(); // Create a DataTransfer object

    selectedFiles.forEach(file => {
        dataTransfer.items.add(file); // Add remaining files to DataTransfer
    });

    input.files = dataTransfer.files; // Update input files
    previewImages({ target: input }); // Refresh the preview
}

</script>
