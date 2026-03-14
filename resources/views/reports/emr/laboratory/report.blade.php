<!DOCTYPE html>
<html>
    <head>
        <style>
        body {
            font-family: Arial;
            font-size: 12px;
        }
        table {
            width:100%;
            border-collapse: collapse;
        }
        th, td {
            border:1px solid #ccc;
            padding:6px;
        }
        .header {
            text-align:center;
            margin-bottom:20px;
        }
        </style>
    </head>
<body>
<div class="header">

<h2>Laboratory Report</h2>

</div>

<h4>Patient Information</h4>

<table>

<tr>
<td>Name</td>
<td>{{ $report['patient']['name'] }}</td>

<td>Gender</td>
<td>{{ $report['patient']['gender'] }}</td>
</tr>

<tr>
<td>Hospital No</td>
<td>{{ $report['patient']['hospital_number'] }}</td>

<td>DOB</td>
<td>{{ $report['patient']['dob'] }}</td>
</tr>

</table>

<br>

<h4>Test Results</h4>

<table>

<thead>

<tr>
<th>Analyte</th>
<th>Value</th>
<th>Unit</th>
<th>Reference Range</th>
<th>Flag</th>
</tr>

</thead>

<tbody>

@foreach($report['values'] as $row)

<tr>

<td>{{ $row['analyte'] }}</td>

<td>{{ $row['value'] }}</td>

<td>{{ $row['unit'] }}</td>

<td>{{ $row['reference_range'] }}</td>

<td>{{ $row['flag'] }}</td>

</tr>

@endforeach

</tbody>

</table>

<br>

<p>

Verified By: {{ $report['verified_by'] }}

</p>

</body>

</html>