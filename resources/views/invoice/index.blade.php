<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title>Invoice - Si Pandawa</title>

    <!-- Invoice styling -->
    <style>
        body {
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            text-align: center;
            color: #777;
        }

        body h1 {
            font-weight: 300;
            margin-bottom: 0px;
            padding-bottom: 0px;
            color: #000;
        }

        body h3 {
            font-weight: 300;
            margin-top: 10px;
            margin-bottom: 20px;
            font-style: italic;
            color: #555;
        }

        body a {
            color: #06f;
        }

        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            font-size: 16px;
            line-height: 24px;
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #555;
        }

        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
            border-collapse: collapse;
        }

        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }

        .invoice-box table tr td:nth-child(2) {
            text-align: right;
        }

        .invoice-box table tr.top table td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.top table td.title {
            font-size: 45px;
            line-height: 45px;
            color: #333;
        }

        .invoice-box table tr.information table td {
            padding-bottom: 40px;
        }

        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }

        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
        }

        .invoice-box table tr.item.last td {
            border-bottom: none;
        }

        .totals {
            text-align: right;
            /* Sejajarkan teks ke kanan */
            margin-right: 30px;
            /* Sesuaikan jarak dari tepi kanan */
            margin-top: 10px;
            /* Tambahkan sedikit jarak dari tabel */
            width: 100%;
            /* Pastikan div mengambil seluruh lebar halaman */
        }

        .totals p {
            margin: 5px 0;
            /* Mengatur jarak antar elemen */
        }

        @media only screen and (max-width: 600px) {
            .invoice-box table tr.top table td {
                width: 100%;
                display: block;
                text-align: center;
            }

            .invoice-box table tr.information table td {
                width: 100%;
                display: block;
                text-align: center;
            }
        }

        .watermark {
            position: absolute;
            top: 70%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 80px;
            color: rgba(15, 233, 15, 0.98);
            white-space: nowrap;
            z-index: -1;
            user-select: none;
            pointer-events: none;
        }

        .detail1 {
            width: 100%;
            border-collapse: collapse;
        }

        .detail1 th,
        .detail1 td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        .detail1 th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
        }

        .detail1 td {
            text-align: center;
        }

        .detail1 .item {
            text-align: left;
            width: 50%;
        }

        .detail1 .detail {
            text-align: left;
            width: 30%;
        }

        .detail1 .total {
            text-align: right;
            width: 20%;
        }
    </style>
</head>

<body>
    @if($invoice->is_paid)
    <div class="watermark">LUNAS</div>
    @endif

    <div class="invoice-box">
        <table>
            <tr class="top">
                <td colspan="2">
                    <table>
                        <tr>
                            <td class="title">
                                <img src="data:image/png;base64,{{ base64_encode(file_get_contents(config('app.url') . '/storage/img/pandawa.png')) }}" alt="Gambar" style="width: 100%; max-width: 300px;" />
                            </td>

                            <td>
                                Invoice #: {{$invoice->no_invoice}}<br />
                                Issued Date: {{$invoice->issued_date}}<br />
                                Due Date: {{$invoice->due_date}} <br />
                                @if($invoice->is_paid)
                                Paid Date: {{$invoice->paid_date}}
                                @endif
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="information">
                <td colspan="2">
                    <table>
                        <tr>
                            <td>
                                From :<br />
                                CV.PANDAWA DIGITAL MEDIA<br />
                                cvpandawadigitalmedia@gmail.com
                            </td>

                            <td>
                                To :<br />
                                {{$invoice->Client->name}}<br />
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="heading">
                <td>Item</td>
                <td>Price</td>
            </tr>
            @foreach($items as $item)
            <tr class="item">
                <td>
                    {{$item->item}}<br />
                </td>
                <td>Rp {{ number_format($item->price, 2, ',', '.') }}</td>

            </tr>
            @endforeach
        </table>
        <div class="totals">
            <p>Sub Total: Rp {{ number_format($invoice->sub_total, 2, ',', '.') }}</p>
            @if($invoice->is_pajak != 0)
            <p>Pajak: Rp {{ number_format($invoice->sub_total*0.11, 2, ',', '.') }}</p>
            @elseif($invoice->diskon != 0)
            <p>Dikon: Rp {{ number_format(10000, 2, ',', '.') }}</p>
            @endif
            <b>
                <p>Total: Rp {{ number_format($invoice->grand_total, 2, ',', '.') }}</p>
            </b>
        </div>
        <br><br>
        <p>Noted :</p>
        <p>BCA : CV PANDAWA DIGITAL MEDIA (1772653021)</p>
    </div>
</body>

</html>