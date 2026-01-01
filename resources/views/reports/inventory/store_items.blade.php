<!DOCTYPE html>
<html>
<head>
    <style>
        table { width:100%; border-collapse: collapse; }
        th, td { border:1px solid #000; padding:5px; text-align:center; font-size: 12px; }
        th { background: #f2f2f2; }
    </style>
</head>
<body>
    <h2 style="text-align:center;">Store Item Report</h2>
    <table>
        <thead>
            <tr>
                <th>Item Name</th>
                <th>Reorder Level</th>
                <th>Maximum Level</th>
                <th>Expiry Notification</th>
                <th>Balance</th>
                <th>Received</th>
                <th>Sold</th>
                <th>Transferred</th>
                <th>Issued</th>
            </tr>
        </thead>
        <tbody>
            @foreach($store_items as $store_item)
                <tr>
                    <td>{{ $store_item->item->name ?? 'Deleted Item' }}</td>
                    <td>{{ $store_item->reorder_level ?? 'N/A' }}</td>
                    <td>{{ $store_item->maximum_level ?? 'N/A' }}</td>
                    <td>{{ $store_item->expiry_notification ?? 'N/A' }}</td>
                    <td>{{ $store_item->total_balance ?? 0 }}</td>
                    <td>{{ $store_item->total_received ?? 0 }}</td>
                    <td>{{ $store_item->total_sold ?? 0 }}</td>
                    <td>{{ $store_item->total_transferred ?? 0 }}</td>
                    <td>{{ $store_item->total_issued ?? 0 }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
