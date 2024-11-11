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
            <div class="row justify-content-around">
                <div class="col-md-10 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Edit Resource</h5>
                        </div>

                        <div class="row card-body content-creation-form">
                            {{-- Assuming you have a route named 'learning-resources.update' --}}
                            <form method="POST" action="{{ route('learning-resources.update', $resource->id) }}" enctype="multipart/form-data">
                                @csrf
                                @method('PUT') {{-- For updating records --}}
                                
                                <div class="row">
                                    {{-- Item Name --}}
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="item_name" class="form-label">Item Name</label>
                                            <input type="text" class="form-control" name="item_name" placeholder="Item Name" value="{{ $resource->item_name }}">
                                        </div>

                                        {{-- Item Category --}}
                                        <div class="mb-3">
                                            <label for="resourceCategory" class="form-label">Item Category</label>
                                            <select class="form-select rounded" id="resourceCategory" name="item_category">
                                                <option disabled="">Select category</option>
                                                <option value="Books" {{ old('item_category', $resource->item_category) == 'Books' ? 'selected' : '' }}>Books</option>
                                                <option value="Stationery" {{ old('item_category', $resource->item_category) == 'Stationery' ? 'selected' : '' }}>Stationery</option>
                                                <option value="Educational Resources" {{ old('item_category', $resource->item_category) == 'Educational Resources' ? 'selected' : '' }}>Educational Resources</option>
                                                <option value="Educational Software" {{ old('item_category', $resource->item_category) == 'Educational Software' ? 'selected' : '' }}>Educational Software</option>
                                                <option value="Electronics" {{ old('item_category', $resource->item_category) == 'Electronics' ? 'selected' : '' }}>Electronics</option>
                                                <option value="Online Courses and Tutorials" {{ old('item_category', $resource->item_category) == 'Online Courses and Tutorials' ? 'selected' : '' }}>Online Courses and Tutorials</option>
                                                <option value="Sporting Equipment" {{ old('item_category', $resource->item_category) == 'Sporting Equipment' ? 'selected' : '' }}>Sporting Equipment</option>
                                                <option value="Other" {{ old('item_category', $resource->item_category) == 'Other' ? 'selected' : '' }}>Other</option>
                                            </select>
                                        </div>
                                    </div>

                                    {{-- County --}}
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="county" class="form-label">County</label>
                                            <select class="form-select rounded" id="county" name="county">
                                                <option disabled="">Select county</option>
                                                <option value="Baringo" {{ old('county', $resource->county) == 'Baringo' ? 'selected' : '' }}>Baringo</option>
                                                <option value="Bomet" {{ old('county', $resource->county) == 'Bomet' ? 'selected' : '' }}>Bomet</option>
                                                <option value="Bungoma" {{ old('county', $resource->county) == 'Bungoma' ? 'selected' : '' }}>Bungoma</option>
                                                <option value="Busia" {{ old('county', $resource->county) == 'Busia' ? 'selected' : '' }}>Busia</option>
                                                <option value="Elgeyo Marakwet" {{ old('county', $resource->county) == 'Elgeyo Marakwet' ? 'selected' : '' }}>Elgeyo Marakwet</option>
                                                <option value="Embu" {{ old('county', $resource->county) == 'Embu' ? 'selected' : '' }}>Embu</option>
                                                <option value="Garissa" {{ old('county', $resource->county) == 'Garissa' ? 'selected' : '' }}>Garissa</option>
                                                <option value="Homa Bay" {{ old('county', $resource->county) == 'Homa Bay' ? 'selected' : '' }}>Homa Bay</option>
                                                <option value="Isiolo" {{ old('county', $resource->county) == 'Isiolo' ? 'selected' : '' }}>Isiolo</option>
                                                <option value="Kajiado" {{ old('county', $resource->county) == 'Kajiado' ? 'selected' : '' }}>Kajiado</option>
                                                <option value="Kakamega" {{ old('county', $resource->county) == 'Kakamega' ? 'selected' : '' }}>Kakamega</option>
                                                <option value="Kericho" {{ old('county', $resource->county) == 'Kericho' ? 'selected' : '' }}>Kericho</option>
                                                <option value="Kiambu" {{ old('county', $resource->county) == 'Kiambu' ? 'selected' : '' }}>Kiambu</option>
                                                <option value="Kilifi" {{ old('county', $resource->county) == 'Kilifi' ? 'selected' : '' }}>Kilifi</option>
                                                <option value="Kirinyaga" {{ old('county', $resource->county) == 'Kirinyaga' ? 'selected' : '' }}>Kirinyaga</option>
                                                <option value="Muranga" {{ old('county', $resource->county) == 'Muranga' ? 'selected' : '' }}>Muranga</option>
                                                <option value="Kisii" {{ old('county', $resource->county) == 'Kisii' ? 'selected' : '' }}>Kisii</option>
                                                <option value="Kisumu" {{ old('county', $resource->county) == 'Kisumu' ? 'selected' : '' }}>Kisumu</option>
                                                <option value="Laikipia" {{ old('county', $resource->county) == 'Laikipia' ? 'selected' : '' }}>Laikipia</option>
                                                <option value="Lamu" {{ old('county', $resource->county) == 'Lamu' ? 'selected' : '' }}>Lamu</option>
                                                <option value="Machakos" {{ old('county', $resource->county) == 'Machakos' ? 'selected' : '' }}>Machakos</option>
                                                <option value="Makueni" {{ old('county', $resource->county) == 'Makueni' ? 'selected' : '' }}>Makueni</option>
                                                <option value="Mandera" {{ old('county', $resource->county) == 'Mandera' ? 'selected' : '' }}>Mandera</option>
                                                <option value="Meru" {{ old('county', $resource->county) == 'Meru' ? 'selected' : '' }}>Meru</option>
                                                <option value="Migori" {{ old('county', $resource->county) == 'Migori' ? 'selected' : '' }}>Migori</option>
                                                <option value="Nairobi" {{ old('county', $resource->county) == 'Nairobi' ? 'selected' : '' }}>Nairobi</option>
                                                <option value="Nakuru" {{ old('county', $resource->county) == 'Nakuru' ? 'selected' : '' }}>Nakuru</option>
                                                <option value="Narok" {{ old('county', $resource->county) == 'Narok' ? 'selected' : '' }}>Narok</option>
                                                <option value="Nembu" {{ old('county', $resource->county) == 'Nembu' ? 'selected' : '' }}>Nembu</option>
                                                <option value="Nyandarua" {{ old('county', $resource->county) == 'Nyandarua' ? 'selected' : '' }}>Nyandarua</option>
                                                <option value="Nyamira" {{ old('county', $resource->county) == 'Nyamira' ? 'selected' : '' }}>Nyamira</option>
                                                <option value="Nairobi" {{ old('county', $resource->county) == 'Nairobi' ? 'selected' : '' }}>Nairobi</option>
                                                <option value="Samburu" {{ old('county', $resource->county) == 'Samburu' ? 'selected' : '' }}>Samburu</option>
                                                <option value="Siaya" {{ old('county', $resource->county) == 'Siaya' ? 'selected' : '' }}>Siaya</option>
                                                <option value="Taita Taveta" {{ old('county', $resource->county) == 'Taita Taveta' ? 'selected' : '' }}>Taita Taveta</option>
                                                <option value="Tana River" {{ old('county', $resource->county) == 'Tana River' ? 'selected' : '' }}>Tana River</option>
                                                <option value="Tharaka Nithi" {{ old('county', $resource->county) == 'Tharaka Nithi' ? 'selected' : '' }}>Tharaka Nithi</option>
                                                <option value="Trans Nzoia" {{ old('county', $resource->county) == 'Trans Nzoia' ? 'selected' : '' }}>Trans Nzoia</option>
                                                <option value="Turkana" {{ old('county', $resource->county) == 'Turkana' ? 'selected' : '' }}>Turkana</option>
                                                <option value="Uasin Gishu" {{ old('county', $resource->county) == 'Uasin Gishu' ? 'selected' : '' }}>Uasin Gishu</option>
                                                <option value="Vihiga" {{ old('county', $resource->county) == 'Vihiga' ? 'selected' : '' }}>Vihiga</option>
                                                <option value="Wajir" {{ old('county', $resource->county) == 'Wajir' ? 'selected' : '' }}>Wajir</option>
                                                <option value="West Pokot" {{ old('county', $resource->county) == 'West Pokot' ? 'selected' : '' }}>West Pokot</option>
                                                <option value="Nairobi City" {{ old('county', $resource->county) == 'Nairobi City' ? 'selected' : '' }}>Nairobi City</option>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label for="item_price" class="form-label">Item Price</label>
                                            <input type="number" class="form-control" name="item_price" placeholder="Item Price" value="{{ $resource->item_price }}">
                                        </div>
                                    </div>

                                    {{-- Contact Info --}}
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="contact_phone" class="form-label">Contact Phone</label>
                                            <input type="number" class="form-control" name="contact_phone" placeholder="Contact Phone" value="{{ old('contact_phone', $resource->contact_phone) }}">
                                        </div>

                                        <div class="mb-3">
                                            <label for="contact_email" class="form-label">Contact Email</label>
                                            <input type="email" class="form-control" name="contact_email" placeholder="Contact Email" value="{{ old('contact_email', $resource->contact_email) }}">
                                        </div>
                                    </div>

                                    <div class="col-md-6">

                                         <div class="mb-3">
                                            <label for="whatsapp_number" class="form-label">WhatsApp Number</label>
                                            <input type="number" class="form-control" name="whatsapp_number" placeholder="WhatsApp Number" value="{{ old('whatsapp_number', $resource->whatsapp_number) }}">
                                        </div>

                                        <div class="mb-3">
                                            <label for="item_thumbnail" class="form-label">Update Thumbnail</label>
                                            <input type="file" id="item_thumbnail" class="form-control" name="item_thumbnail" accept="image/*">
                                        </div>
                                    </div>

                                      {{-- Item Images --}}
                                      <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="item_images" class="form-label">Item Images</label>
                                            <input type="file" id="item_images" class="form-control" name="item_images[]" accept="image/*" multiple>

                                            <div id="media-preview" class="mt-3 d-flex flex-wrap">
                                                @if(is_array(json_decode($resource->item_images, true))) {{-- Check if item_images is stored as a JSON array --}}
                                                    @foreach (json_decode($resource->item_images, true) as $image)
                                                        <img src="{{ Storage::url($image) }}" alt="Item Image" class="img-thumbnail" style="width: 100px; height: auto; margin-right: 10px;">
                                                    @endforeach
                                                @else
                                                    <p>No images available</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Description --}}
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="description" class="form-label">Description</label>
                                            <textarea name="description" class="form-control" rows="5">{{ old('description', $resource->description) }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                {{-- Update Button --}}
                                <div class="text-center mt-3">
                                    <input type="submit" class="btn btn-primary" value="Update Item" />
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