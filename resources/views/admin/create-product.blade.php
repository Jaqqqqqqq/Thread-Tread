@extends('layouts.app')

@section('content')
<div style="padding: 48px 32px;">
    <div style="background: #fff; border-radius: 8px; padding: 32px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
        <h2 style="margin: 0 0 32px 0; background: #333; color: #fff; padding: 16px; margin: -32px -32px 32px -32px; border-radius: 8px 8px 0 0;">Add New Product</h2>
        
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

        <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
            @csrf

            <!-- Brand and Category Row -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label for="brand_id" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">Brand</label>
                    <select id="brand_id" name="brand_id" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
                        <option value="">Select Brand</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->brand_id }}" {{ old('brand_id') == $brand->brand_id ? 'selected' : '' }}>{{ $brand->brand_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="category_id" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">Category</label>
                    <select id="category_id" name="category_id" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->category_id }}" {{ old('category_id') == $category->category_id ? 'selected' : '' }}>{{ $category->category_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Product Name -->
            <div style="margin-bottom: 20px;">
                <label for="product_name" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">Product Name</label>
                <input type="text" id="product_name" name="product_name" value="{{ old('product_name') }}" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
            </div>

            <!-- Price -->
            <div style="margin-bottom: 20px;">
                <label for="price" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">Price (base)</label>
                <input type="number" id="price" name="price" value="{{ old('price') }}" step="0.01" min="0" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
            </div>

            <!-- Description -->
            <div style="margin-bottom: 20px;">
                <label for="prod_desc" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">Description</label>
                <textarea id="prod_desc" name="prod_desc" rows="5" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;">{{ old('prod_desc') }}</textarea>
            </div>

            <!-- Product Images -->
            <div style="margin-bottom: 30px;">
                <label for="product_images" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">Product Images</label>
                <input type="file" id="product_images" name="product_images[]" accept="image/*" multiple style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
                <small style="color: #666; display: block; margin-top: 6px;">Select one or more images. Accepted formats: JPG, PNG, GIF, WebP (Max 2MB each)</small>
                <div id="imagePreview" style="display: flex; gap: 10px; margin-top: 12px; flex-wrap: wrap;"></div>
            </div>

            <!-- Main Product Image (kept for backward compatibility) -->
            <div style="margin-bottom: 30px;">
                <label for="prod_image" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">Primary Product Image (optional)</label>
                <input type="file" id="prod_image" name="prod_image" accept="image/*" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
                <small style="color: #666; display: block; margin-top: 6px;">This is the main/thumbnail image. If not selected, the first image from above will be used. Accepted formats: JPG, PNG, GIF, WebP (Max 2MB)</small>
            </div>

            <!-- Variants Section -->
            <div style="margin-bottom: 30px;">
                <h3 style="margin: 0 0 20px 0; color: #333;">Variants (Color + Size + Stock)</h3>
                <div id="variantsContainer">
                    <div class="variant-row" style="display: grid; grid-template-columns: 1.5fr 1.5fr 1fr 1fr auto; gap: 12px; margin-bottom: 15px; align-items: flex-end;">
                        <div>
                            <label style="display: block; margin-bottom: 6px; font-weight: 500; font-size: 13px; color: #555;">Color Name</label>
                            <input type="text" name="variants[0][color]" placeholder="e.g., Red" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 13px; box-sizing: border-box;">
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 6px; font-weight: 500; font-size: 13px; color: #555;">Color Image</label>
                            <input type="file" name="variants[0][color_image]" accept="image/*" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 13px; box-sizing: border-box;">
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 6px; font-weight: 500; font-size: 13px; color: #555;">Size</label>
                            <input type="text" name="variants[0][size]" placeholder="e.g., 38" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 13px; box-sizing: border-box;">
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 6px; font-weight: 500; font-size: 13px; color: #555;">Stock</label>
                            <input type="number" name="variants[0][stock]" value="0" min="0" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 13px; box-sizing: border-box;">
                        </div>
                        <button type="button" class="remove-variant" style="display: none; background: #7b1fa2; color: #fff; border: none; width: 40px; height: 40px; border-radius: 4px; cursor: pointer; font-weight: bold; font-size: 18px; align-self: flex-end;">✕</button>
                    </div>
                </div>
                <button type="button" id="addVariantBtn" style="background: #2e7d32; color: #fff; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-weight: 600; font-size: 14px; margin-top: 10px;">+ Add Variant</button>
            </div>

            <!-- Submit Button -->
            <button type="submit" style="width: 100%; padding: 14px; background: #2e7d32; color: #fff; border: none; border-radius: 4px; font-size: 16px; cursor: pointer; font-weight: 600;">+ Add Product</button>
        </form>
    </div>
</div>

<script>
    let variantCount = 1;
    
    // Image preview functionality
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
    
    document.getElementById('addVariantBtn').addEventListener('click', function(e) {
        e.preventDefault();
        const container = document.getElementById('variantsContainer');
        const newVariant = document.createElement('div');
        newVariant.className = 'variant-row';
        newVariant.style.cssText = 'display: grid; grid-template-columns: 1.5fr 1.5fr 1fr 1fr auto; gap: 12px; margin-bottom: 15px; align-items: flex-end;';
        newVariant.innerHTML = `
            <div>
                <label style="display: block; margin-bottom: 6px; font-weight: 500; font-size: 13px; color: #555;">Color Name</label>
                <input type="text" name="variants[${variantCount}][color]" placeholder="e.g., Red" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 13px; box-sizing: border-box;">
            </div>
            <div>
                <label style="display: block; margin-bottom: 6px; font-weight: 500; font-size: 13px; color: #555;">Color Image</label>
                <input type="file" name="variants[${variantCount}][color_image]" accept="image/*" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 13px; box-sizing: border-box;">
            </div>
            <div>
                <label style="display: block; margin-bottom: 6px; font-weight: 500; font-size: 13px; color: #555;">Size</label>
                <input type="text" name="variants[${variantCount}][size]" placeholder="e.g., 38" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 13px; box-sizing: border-box;">
            </div>
            <div>
                <label style="display: block; margin-bottom: 6px; font-weight: 500; font-size: 13px; color: #555;">Stock</label>
                <input type="number" name="variants[${variantCount}][stock]" value="0" min="0" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 13px; box-sizing: border-box;">
            </div>
            <button type="button" class="remove-variant" style="background: #7b1fa2; color: #fff; border: none; width: 40px; height: 40px; border-radius: 4px; cursor: pointer; font-weight: bold; font-size: 18px; align-self: flex-end;">✕</button>
        `;
        container.appendChild(newVariant);
        variantCount++;
        updateRemoveButtons();
    });

    function updateRemoveButtons() {
        const variants = document.querySelectorAll('.variant-row');
        variants.forEach((variant) => {
            const removeBtn = variant.querySelector('.remove-variant');
            if (variants.length > 1) {
                removeBtn.style.display = 'block';
            } else {
                removeBtn.style.display = 'none';
            }
        });
    }

    document.getElementById('variantsContainer').addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-variant')) {
            e.preventDefault();
            e.target.closest('.variant-row').remove();
            updateRemoveButtons();
        }
    });

    updateRemoveButtons();
</script>
@endsection
