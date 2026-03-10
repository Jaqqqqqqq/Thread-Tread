@extends('layouts.app')

@section('content')
<div style="padding: 48px 32px;">
    <div style="background: #fff; border-radius: 8px; padding: 32px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
        <h2 style="margin: 0 0 32px 0; background: #333; color: #fff; padding: 16px; margin: -32px -32px 32px -32px; border-radius: 8px 8px 0 0;">Import Products from Excel</h2>
        
        @if ($errors->any())
            <div style="background: #ffebee; padding: 12px 16px; border-radius: 4px; color: #c62828; margin-bottom: 20px;">
                <strong>Error:</strong>
                <ul style="margin: 8px 0 0 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div style="background: #c8e6c9; padding: 12px 16px; border-radius: 4px; color: #2e7d32; margin-bottom: 20px;">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div style="background: #ffcdd2; padding: 12px 16px; border-radius: 4px; color: #c62828; margin-bottom: 20px;">
                {{ session('error') }}
            </div>
        @endif

        <!-- Instructions -->
        <div style="background: #e3f2fd; padding: 16px; border-radius: 4px; margin-bottom: 30px; border-left: 4px solid #1976d2;">
            <h3 style="margin: 0 0 12px 0; color: #1565c0; font-size: 16px;">📋 Import Instructions</h3>
            <p style="margin: 0 0 12px 0; font-size: 14px; color: #333;">
                Import multiple products at once using an Excel file. The file should contain the following columns:
            </p>
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 12px;">
                <thead>
                    <tr style="background: #bbdefb;">
                        <th style="padding: 8px 12px; text-align: left; font-weight: 600; font-size: 13px; border: 1px solid #90caf9;">Column Name</th>
                        <th style="padding: 8px 12px; text-align: left; font-weight: 600; font-size: 13px; border: 1px solid #90caf9;">Required</th>
                        <th style="padding: 8px 12px; text-align: left; font-weight: 600; font-size: 13px; border: 1px solid #90caf9;">Description</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="border-bottom: 1px solid #e3f2fd;">
                        <td style="padding: 8px 12px; font-size: 13px; border: 1px solid #e0e0e0;"><code style="background: #f5f5f5; padding: 2px 4px; border-radius: 2px;">product_name</code></td>
                        <td style="padding: 8px 12px; font-size: 13px; border: 1px solid #e0e0e0;"><span style="background: #f44336; color: #fff; padding: 2px 6px; border-radius: 2px; font-size: 12px;">Yes</span></td>
                        <td style="padding: 8px 12px; font-size: 13px; border: 1px solid #e0e0e0;">Name of the product</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 12px; font-size: 13px; border: 1px solid #e0e0e0;"><code style="background: #f5f5f5; padding: 2px 4px; border-radius: 2px;">prod_desc</code></td>
                        <td style="padding: 8px 12px; font-size: 13px; border: 1px solid #e0e0e0;"><span style="background: #999; color: #fff; padding: 2px 6px; border-radius: 2px; font-size: 12px;">No</span></td>
                        <td style="padding: 8px 12px; font-size: 13px; border: 1px solid #e0e0e0;">Product description</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 12px; font-size: 13px; border: 1px solid #e0e0e0;"><code style="background: #f5f5f5; padding: 2px 4px; border-radius: 2px;">price</code></td>
                        <td style="padding: 8px 12px; font-size: 13px; border: 1px solid #e0e0e0;"><span style="background: #f44336; color: #fff; padding: 2px 6px; border-radius: 2px; font-size: 12px;">Yes</span></td>
                        <td style="padding: 8px 12px; font-size: 13px; border: 1px solid #e0e0e0;">Product price (numeric)</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 12px; font-size: 13px; border: 1px solid #e0e0e0;"><code style="background: #f5f5f5; padding: 2px 4px; border-radius: 2px;">category_name</code></td>
                        <td style="padding: 8px 12px; font-size: 13px; border: 1px solid #e0e0e0;"><span style="background: #f44336; color: #fff; padding: 2px 6px; border-radius: 2px; font-size: 12px;">Yes</span></td>
                        <td style="padding: 8px 12px; font-size: 13px; border: 1px solid #e0e0e0;">Name of an existing category</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 12px; font-size: 13px; border: 1px solid #e0e0e0;"><code style="background: #f5f5f5; padding: 2px 4px; border-radius: 2px;">brand_name</code></td>
                        <td style="padding: 8px 12px; font-size: 13px; border: 1px solid #e0e0e0;"><span style="background: #999; color: #fff; padding: 2px 6px; border-radius: 2px; font-size: 12px;">No</span></td>
                        <td style="padding: 8px 12px; font-size: 13px; border: 1px solid #e0e0e0;">Name of an existing brand</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 12px; font-size: 13px; border: 1px solid #e0e0e0;"><code style="background: #f5f5f5; padding: 2px 4px; border-radius: 2px;">prod_image</code></td>
                        <td style="padding: 8px 12px; font-size: 13px; border: 1px solid #e0e0e0;"><span style="background: #999; color: #fff; padding: 2px 6px; border-radius: 2px; font-size: 12px;">No</span></td>
                        <td style="padding: 8px 12px; font-size: 13px; border: 1px solid #e0e0e0;">Local file path to product image</td>
                    </tr>
                </tbody>
            </table>
            <p style="margin: 0; font-size: 13px; color: #333;">
                ℹ️ <strong>Note:</strong> Products with missing required fields will be skipped. All category names must already exist in the system.
            </p>
        </div>

        <!-- Import Form -->
        <form method="POST" action="{{ route('admin.products.import.store') }}" enctype="multipart/form-data">
            @csrf

            <div style="margin-bottom: 20px;">
                <label for="excel_file" style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">Select Excel File</label>
                <input type="file" id="excel_file" name="excel_file" accept=".xlsx,.xls,.csv" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
                <small style="color: #666; display: block; margin-top: 6px;">Accepted formats: XLSX, XLS, CSV</small>
            </div>

            <div style="display: flex; gap: 12px;">
                <button type="submit" style="flex: 1; padding: 14px; background: #2e7d32; color: #fff; border: none; border-radius: 4px; font-size: 16px; cursor: pointer; font-weight: 600;">📤 Import Products</button>
                <a href="{{ route('admin.products') }}" style="flex: 1; padding: 14px; background: #ddd; color: #222; border: none; border-radius: 4px; font-size: 16px; text-align: center; text-decoration: none; font-weight: 600; line-height: 1.2;">Cancel</a>
            </div>
        </form>

        <!-- Example Template -->
        <div style="margin-top: 40px; padding-top: 30px; border-top: 1px solid #e0e0e0;">
            <h3 style="margin: 0 0 16px 0; color: #333;">📝 Example Format</h3>
            <p style="font-size: 13px; color: #666; margin: 0 0 12px 0;">Here's an example of how your Excel file should be formatted:</p>
            <div style="overflow-x: auto; background: #f5f5f5; border-radius: 4px; border: 1px solid #ddd;">
                <table style="width: 100%; border-collapse: collapse; font-size: 12px; min-width: 600px;">
                    <thead>
                        <tr style="background: #e0e0e0;">
                            <th style="padding: 10px; text-align: left; border: 1px solid #bdbdbd; font-weight: 600;">product_name</th>
                            <th style="padding: 10px; text-align: left; border: 1px solid #bdbdbd; font-weight: 600;">prod_desc</th>
                            <th style="padding: 10px; text-align: left; border: 1px solid #bdbdbd; font-weight: 600;">price</th>
                            <th style="padding: 10px; text-align: left; border: 1px solid #bdbdbd; font-weight: 600;">category_name</th>
                            <th style="padding: 10px; text-align: left; border: 1px solid #bdbdbd; font-weight: 600;">brand_name</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="padding: 8px 10px; border: 1px solid #bdbdbd;">Classic White T-Shirt</td>
                            <td style="padding: 8px 10px; border: 1px solid #bdbdbd;">100% cotton, comfortable fit</td>
                            <td style="padding: 8px 10px; border: 1px solid #bdbdbd;">299.99</td>
                            <td style="padding: 8px 10px; border: 1px solid #bdbdbd;">T-Shirts</td>
                            <td style="padding: 8px 10px; border: 1px solid #bdbdbd;">Nike</td>
                        </tr>
                        <tr style="background: #fafafa;">
                            <td style="padding: 8px 10px; border: 1px solid #bdbdbd;">Blue Denim Jeans</td>
                            <td style="padding: 8px 10px; border: 1px solid #bdbdbd;">Slim fit denim</td>
                            <td style="padding: 8px 10px; border: 1px solid #bdbdbd;">1499.99</td>
                            <td style="padding: 8px 10px; border: 1px solid #bdbdbd;">Jeans</td>
                            <td style="padding: 8px 10px; border: 1px solid #bdbdbd;">Levi's</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 10px; border: 1px solid #bdbdbd;">Summer Dress</td>
                            <td style="padding: 8px 10px; border: 1px solid #bdbdbd;">Lightweight, floral design</td>
                            <td style="padding: 8px 10px; border: 1px solid #bdbdbd;">799.99</td>
                            <td style="padding: 8px 10px; border: 1px solid #bdbdbd;">Dresses</td>
                            <td style="padding: 8px 10px; border: 1px solid #bdbdbd;"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
