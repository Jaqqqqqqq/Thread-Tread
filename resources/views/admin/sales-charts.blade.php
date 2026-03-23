@extends('layouts.app')

@section('content')
<div style="padding: 48px 32px;">
    <h2>Sales Analytics Dashboard</h2>
    
    <div style="margin-bottom: 40px;">
        <h3>Yearly Sales 2026</h3>
        <div style="background: white; padding: 20px; border-radius: 8px;">
            {!! $yearlySalesChart->container() !!}
        </div>
    </div>

    <div style="margin-bottom: 40px;">
        <h3>Sales by Date Range (Date Picker)</h3>
        <div style="background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
            <form id="dateRangeForm" method="GET" action="{{ route('admin.sales.charts') }}" style="display: flex; gap: 10px; align-items: center;">
                <label for="startDate">From:</label>
                <input type="date" id="startDate" name="start_date" value="{{ request('start_date', date('Y-m-d', strtotime('-30 days'))) }}" style="padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                
                <label for="endDate">To:</label>
                <input type="date" id="endDate" name="end_date" value="{{ request('end_date', date('Y-m-d')) }}" style="padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                
                <button type="submit" style="padding: 8px 20px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">Filter</button>
            </form>
        </div>
        <div style="background: white; padding: 20px; border-radius: 8px;">
            {!! $salesRangeChart->container() !!}
        </div>
    </div>

    <div style="margin-bottom: 40px;">
        <h3>Sales by Product - Percentage Distribution</h3>
        <div style="background: white; padding: 20px; border-radius: 8px;">
            {!! $productPieChart->container() !!}
        </div>
    </div>
</div>

{!! $yearlySalesChart->script() !!}
{!! $salesRangeChart->script() !!}
{!! $productPieChart->script() !!}
@endsection
