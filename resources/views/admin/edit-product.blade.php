@extends('layouts.app')

@section('content')
<div style="padding: 48px 32px;">
    <div style="background: #fff; border-radius: 8px; padding: 32px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
        <h2 style="margin: 0 0 32px 0; background: #333; color: #fff; padding: 16px; margin: -32px -32px 32px -32px; border-radius: 8px 8px 0 0;">Edit Product</h2>
        
        @if ($errors->any())
            <div style="background: #ffebee; padding: 12px 16px; border-radius: 4px; color: #c62828; margin-bottom: 20px;">
                <strong>Please fix the following errors:</strong>
                <ul style="margin: 8px 0 0 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.products.update', $product->product_id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Brand and Category Row -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label for="brand_id" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">Brand</label>
                    <select id="brand_id" name="brand_id" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
                        <option value="">Select Brand</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->brand_id }}" {{ old('brand_id', $product->brand_id) == $brand->brand_id ? 'selected' : '' }}>{{ $brand->brand_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="category_id" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">Category</label>
                    <select id="category_id" name="category_id" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->category_id }}" {{ old('category_id', $product->category_id) == $category->category_id ? 'selected' : '' }}>{{ $category->category_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Product Name -->
            <div style="margin-bottom: 20px;">
                <label for="product_name" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">Product Name</label>
                <input type="text" id="product_name" name="product_name" value="{{ old('product_name', $product->product_name) }}" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
            </div>

            <!-- Price -->
            <div style="margin-bottom: 20px;">
                <label for="price" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">Price (₱)</label>
                <input type="number" id="price" name="price" value="{{ old('price', $product->price) }}" step="0.01" min="0" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
            </div>

            <!-- Description -->
            <div style="margin-bottom: 20px;">
                <label for="prod_desc" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">Description</label>
                <textarea id="prod_desc" name="prod_desc" rows="5" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;">{{ old('prod_desc', $product->prod_desc) }}</textarea>
            </div>

            <!-- Current Image Preview + Upload New -->
            <div style="margin-bottom: 30px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">Primary Product Image</label>
                @if($product->prod_image)
                    <div style="margin-bottom: 12px;">
                        <img src="{{ asset($product->prod_image) }}" alt="{{ $product->product_name }}" style="max-width: 200px; max-height: 200px; border-radius: 6px; border: 1px solid #ddd;">
                        <p style="margin: 8px 0 0 0; font-size: 12px; color: #666;">Current image</p>
                    </div>
                @endif
                <input type="file" id="prod_image" name="prod_image" accept="image/*" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
                <small style="color: #666; display: block; margin-top: 6px;">Leave blank to keep current image. Accepted formats: JPG, PNG, GIF, WebP (Max 2MB)</small>
            </div>

            <!-- Product Gallery Images -->
            <div style="margin-bottom: 30px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">Product Gallery Images</label>
                <p style="font-size: 13px; color: #666; margin: 0 0 12px 0;">Manage additional product images</p>
                
                @if($images->count() > 0)
                    <div style="margin-bottom: 16px;">
                        <h4 style="margin: 0 0 12px 0; color: #555; font-size: 13px;">Existing Images</h4>
                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap: 12px;">
                            @foreach($images as $image)
                                <div style="position: relative; border-radius: 6px; overflow: hidden; border: 1px solid #ddd;">
                                    <img src="{{ asset($image->image_path) }}" alt="Product" style="width: 100%; height: 100px; object-fit: cover;">
                                    <label style="position: absolute; top: 4px; right: 4px; cursor: pointer;">
                                        <input type="checkbox" name="delete_images[]" value="{{ $image->image_id }}" style="margin: 4px;">
                                        <span style="background: rgba(0,0,0,0.7); color: #fff; padding: 2px 6px; border-radius: 3px; font-size: 11px; display: inline-block;">Delete</span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div style="background: #f0f0f0; padding: 16px; border-radius: 4px; color: #666; margin-bottom: 16px; font-size: 13px;">
                        No gallery images yet. Add some below.
                    </div>
                @endif
                
                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #333; font-size: 13px;">Add New Images</label>
                <input type="file" id="product_images" name="product_images[]" accept="image/*" multiple style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
                <small style="color: #666; display: block; margin-top: 6px;">Select one or more images to add to the gallery</small>
                <div id="imagePreview" style="display: flex; gap: 10px; margin-top: 12px; flex-wrap: wrap;"></div>
            </div>

            <!-- Submit Buttons -->
            <div style="display: flex; gap: 12px;">
                <button type="submit" style="flex: 1; padding: 14px; background: #2e7d32; color: #fff; border: none; border-radius: 4px; font-size: 16px; cursor: pointer; font-weight: 600;">Update Product</button>
                <a href="{{ route('admin.products') }}" style="flex: 1; padding: 14px; background: #ddd; color: #222; border: none; border-radius: 4px; font-size: 16px; text-align: center; text-decoration: none; font-weight: 600; line-height: 1.2;">Cancel</a>
            </div>
        </form>

            <!-- Variants Section (outside main form to avoid nested form issues) -->
            <div style="margin: 30px 0;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <h3 style="margin: 0; color: #333;">Product Variants</h3>
                    <button type="button" onclick="openAddVariantModal()" style="padding: 8px 16px; background: #1976d2; color: #fff; border: none; border-radius: 4px; font-size: 13px; cursor: pointer; font-weight: 600;">+ Add Variant</button>
                </div>

            @if($variants->count() > 0)
                <div>
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse; border: 1px solid #ddd; border-radius: 4px;">
                            <thead>
                                <tr style="background: #f5f5f5; border-bottom: 1px solid #ddd;">
                                    <th style="padding: 10px 14px; text-align: left; font-weight: 600; font-size: 13px;">Color</th>
                                    <th style="padding: 10px 14px; text-align: left; font-weight: 600; font-size: 13px;">Size</th>
                                    <th style="padding: 10px 14px; text-align: left; font-weight: 600; font-size: 13px;">Stock</th>
                                    <th style="padding: 10px 14px; text-align: left; font-weight: 600; font-size: 13px;">Color Image</th>
                                    <th style="padding: 10px 14px; text-align: left; font-weight: 600; font-size: 13px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($variants as $variant)
                                    <tr style="border-bottom: 1px solid #eee;">
                                        <td style="padding: 10px 14px; font-size: 13px;">{{ $variant->color ?? '—' }}</td>
                                        <td style="padding: 10px 14px; font-size: 13px;">{{ $variant->size ?? '—' }}</td>
                                        <td style="padding: 10px 14px; font-size: 13px;">
                                            <span style="background: {{ $variant->stock > 0 ? '#c8e6c9' : '#ffcdd2' }}; padding: 2px 8px; border-radius: 3px; font-size: 12px;">
                                                {{ $variant->stock }}
                                            </span>
                                        </td>
                                        <td style="padding: 10px 14px;">
                                            @if($variant->color_image)
                                                <img src="{{ asset($variant->color_image) }}" alt="{{ $variant->color }}" style="height: 30px; border-radius: 3px;">
                                            @else
                                                <span style="color: #999; font-size: 12px;">—</span>
                                            @endif
                                        </td>
                                        <td style="padding: 10px 14px;">
                                            <div style="display: flex; gap: 6px;">
                                                <button type="button" onclick="openVariantModal({{ $variant->variant_id }}, '{{ $variant->color }}', '{{ $variant->size }}', {{ $variant->stock }}, '{{ $variant->color_image }}')" style="padding: 4px 8px; background: #1976d2; color: #fff; border: none; border-radius: 3px; text-decoration: none; font-size: 12px; cursor: pointer; font-weight: 500;">Edit</button>
                                                <form method="POST" action="{{ route('admin.variants.destroy', $variant->variant_id) }}" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" style="padding: 4px 8px; background: #d32f2f; color: #fff; border: none; border-radius: 3px; text-decoration: none; font-size: 12px; cursor: pointer; font-weight: 500;" class="delete-variant-btn">Delete</button>
                                                @push('scripts')
                                                <script>
                                                    document.addEventListener('DOMContentLoaded', function() {
                                                        document.querySelectorAll('.delete-variant-btn').forEach(function(btn) {
                                                            btn.addEventListener('click', function(e) {
                                                                e.preventDefault();
                                                                const form = btn.closest('form');
                                                                Swal.fire({
                                                                    title: 'Delete Variant',
                                                                    text: 'Are you sure you want to delete this variant?',
                                                                    icon: 'warning',
                                                                    showCancelButton: true,
                                                                    confirmButtonColor: '#d32f2f',
                                                                    cancelButtonColor: '#aaa',
                                                                    confirmButtonText: 'Yes, delete',
                                                                    cancelButtonText: 'Cancel',
                                                                }).then((result) => {
                                                                    if (result.isConfirmed) {
                                                                        form.submit();
                                                                    }
                                                                });
                                                            });
                                                        });
                                                    });
                                                </script>
                                                @endpush
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div style="background: #f0f0f0; padding: 16px; border-radius: 4px; color: #666; font-size: 13px;">
                    No variants yet. Click "+ Add Variant" to add one.
                </div>
            @endif
            </div>

            <!-- Add Variant Modal -->
            <div id="addVariantModalOverlay" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 999; justify-content: center; align-items: center;">
                <div style="background: #fff; border-radius: 8px; padding: 32px; box-shadow: 0 4px 20px rgba(0,0,0,0.15); max-width: 500px; width: 90%; max-height: 90vh; overflow-y: auto; position: relative;">
                    <button type="button" onclick="closeAddVariantModal()" style="position: absolute; top: 12px; right: 12px; background: none; border: none; font-size: 24px; cursor: pointer; color: #666;">&times;</button>
                    
                    <h3 style="margin: 0 0 24px 0; color: #333;">Add New Variant</h3>

                    <form method="POST" action="{{ route('admin.variants.store') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->product_id }}">

                        <!-- Color -->
                        <div style="margin-bottom: 20px;">
                            <label for="add_color" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">Color</label>
                            <input type="text" id="add_color" name="color" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
                        </div>

                        <!-- Size -->
                        <div style="margin-bottom: 20px;">
                            <label for="add_size" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">Size</label>
                            <input type="text" id="add_size" name="size" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
                        </div>

                        <!-- Stock -->
                        <div style="margin-bottom: 20px;">
                            <label for="add_stock" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">Stock Quantity</label>
                            <input type="number" id="add_stock" name="stock" min="0" value="0" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
                        </div>

                        <!-- Buttons -->
                        <div style="display: flex; gap: 12px;">
                            <button type="submit" style="flex: 1; padding: 12px; background: #2e7d32; color: #fff; border: none; border-radius: 4px; font-size: 14px; cursor: pointer; font-weight: 600;">Add Variant</button>
                            <button type="button" onclick="closeAddVariantModal()" style="flex: 1; padding: 12px; background: #ddd; color: #222; border: none; border-radius: 4px; font-size: 14px; cursor: pointer; font-weight: 600;">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Edit Variant Modal -->
            <div id="variantModalOverlay" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 999; justify-content: center; align-items: center;">
                <div style="background: #fff; border-radius: 8px; padding: 32px; box-shadow: 0 4px 20px rgba(0,0,0,0.15); max-width: 500px; width: 90%; max-height: 90vh; overflow-y: auto; position: relative;">
                    <button type="button" onclick="closeVariantModal()" style="position: absolute; top: 12px; right: 12px; background: none; border: none; font-size: 24px; cursor: pointer; color: #666;">&times;</button>
                    
                    <h3 style="margin: 0 0 24px 0; color: #333;">Edit Variant</h3>

                    <form id="variantEditForm" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Color -->
                        <div style="margin-bottom: 20px;">
                            <label for="modal_color" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">Color</label>
                            <input type="text" id="modal_color" name="color" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
                        </div>

                        <!-- Size -->
                        <div style="margin-bottom: 20px;">
                            <label for="modal_size" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">Size</label>
                            <input type="text" id="modal_size" name="size" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
                        </div>

                        <!-- Stock -->
                        <div style="margin-bottom: 20px;">
                            <label for="modal_stock" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">Stock Quantity</label>
                            <input type="number" id="modal_stock" name="stock" min="0" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
                        </div>

                        <!-- Buttons -->
                        <div style="display: flex; gap: 12px;">
                            <button type="submit" style="flex: 1; padding: 12px; background: #2e7d32; color: #fff; border: none; border-radius: 4px; font-size: 14px; cursor: pointer; font-weight: 600;">Save Changes</button>
                            <button type="button" onclick="closeVariantModal()" style="flex: 1; padding: 12px; background: #ddd; color: #222; border: none; border-radius: 4px; font-size: 14px; cursor: pointer; font-weight: 600;">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>

    </div>
</div>

<script>
    // Image preview functionality for new images
    document.getElementById('product_images').addEventListener('change', function(e) {
        const preview = document.getElementById('imagePreview');
        preview.innerHTML = '';
        
        Array.from(this.files).forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(event) {
                const img = document.createElement('img');
                img.src = event.target.result;
                img.style.cssText = 'height: 80px; width: 80px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd;';
                preview.appendChild(img);
            };
            reader.readAsDataURL(file);
        });
    });

    // Modal functionality for variant editing
    function openVariantModal(variantId, color, size, stock, colorImage) {
        document.getElementById('modal_color').value = color;
        document.getElementById('modal_size').value = size;
        document.getElementById('modal_stock').value = stock;
        // Set the form action URL
        const form = document.getElementById('variantEditForm');
        form.action = `/admin/variants/${variantId}/update`;

        // Show modal
        document.getElementById('variantModalOverlay').style.display = 'flex';
    }

    function closeVariantModal() {
        document.getElementById('variantModalOverlay').style.display = 'none';
        document.getElementById('variantEditForm').reset();
    }

    // Close modal when clicking outside of it
    document.getElementById('variantModalOverlay').addEventListener('click', function(e) {
        if (e.target === this) {
            closeVariantModal();
        }
    });

    // Add Variant Modal functions
    function openAddVariantModal() {
        document.getElementById('addVariantModalOverlay').style.display = 'flex';
    }

    function closeAddVariantModal() {
        document.getElementById('addVariantModalOverlay').style.display = 'none';
    }

    // Close add variant modal when clicking outside
    document.getElementById('addVariantModalOverlay').addEventListener('click', function(e) {
        if (e.target === this) {
            closeAddVariantModal();
        }
    });
</script>
@endsection