<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="viewport" content="width=1080px, initial-scale=1.0">
    <title>Invoice</title>
    <style>
        :root {
            --bg-color: #eee;
        }
        body { margin: 15px; }
        table {
            margin-top: 10px;
            width: 100%;
            direction: rtl;
            border-collapse: collapse;
            font-family: vazir;
        }

        th,td {
            margin: 0;
            padding: 3px;
            text-align: center;
            border: #d2d7dc solid 1px;
            font-size: 10pt;
        }

        th { font-weight: bold; }
        tr { border-top: #d2d7dc solid 1px; }
        tr:nth-child(even) { background-color: var(--bg-color); }

        #shop-info ul {
            list-style: none;
            margin: 0;
            padding: 0 15px;
        }
        #shop-info ul li { display: flex; justify-content: space-between; }
        #shop-info h1 { font-weight: 900; margin: 5px 0; font-size: 26pt; }
        #shop-info td { border-width: 1px 0; width: 33%; }
        #shop-info td:last-child { border-left-width: 1px; }
        #shop-info td:first-child { border-right-width: 1px; }
        #shop-info td { background-color: var(--bg-color) }
        #logo { height: 70px; padding: 0 15px; }
    </style>
</head>

<?php
    global $woocommerce;

    $items = $woocommerce->cart->get_cart();
    $currentUser = wp_get_current_user();
    $storePostcode = get_option('woocommerce_store_postcode', "-");
    $logoUrl = get_option("smr_config_option", [])['site_logo'];

    global $logo_url;
?>
<body>
<table id="shop-info">
    <thead>
        <tr>
            <td>
                <img id="logo" src="<?= $logoUrl ?>">
            </td>
            <td>
                <h1><?= get_bloginfo('name'); ?></h1>
                <small>
                    <i><?= get_bloginfo('description'); ?></i>
                </small>
            </td>
            <td style="text-align:right;">
                <ul>
                    <li><label>تاریخ:</label><span><?= wp_date("Y/m/d") ?></span></li>
                    <li><label>ساعت:</label><span><?= wp_date("g:i:s") ?></span></li>
                    <li><label>شماره فاکتور:</label><span>-</span></li>
                    <li><label>تلفن فروشگاه:</label><span dir="ltr">071 3231 1146</span></li>
                    <li><label>کد پستی فروشگاه:</label><span dir="ltr"><?= $storePostcode; ?></span></li>
                    <li><label>نام کاربری:</label><span><?= $currentUser->user_login; ?></span></li>
                </ul>
            </td>
        </tr>
    </thead>
</table>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>کد</th>
            <th>نام</th>
            <th>تعداد</th>
            <th>قیمت (تومان)</th>
            <th>قیمت کل (تومان)</th>
            <th>تخفیف</th>
            <th>قیمت نهایی (تومان)</th>
        </tr>
    </thead>
    <tbody>
        <?php
            $row = 1;
            $totalQuantity = 0;
            $totalPrice = 0;
            foreach($items as $itemKey => $item) {
                $id = $item['product_id'];

                $product = wc_get_product($id);
                $title = $product->get_title();

                $reqularPrice = $product->get_regular_price();
                $salePrice = is_numeric($product->get_sale_price()) ? $product->get_sale_price() : $reqularPrice;

                $quantity = $item['quantity'] ?? 0;
                $price = $item['line_total']; // quantity * salePrice

                $off = round((($reqularPrice - $salePrice) / $reqularPrice) * 100, 1);
                ?><tr>
                    <td><?= $row++ ?></td>
                    <td><?= $id ?></td>
                    <td><?= $title ?></td>
                    <td><?= $quantity ?></td>
                    <td><?= number_format($reqularPrice) ?></td>
                    <td><?= number_format($reqularPrice * $quantity) ?></td>
                    <td><?= $off ? $off."%" : "-" ?></td>
                    <td><?= number_format($price) ?></td>
                </tr><?php

                $totalQuantity += $quantity;
                $totalPrice += $price;
            }
        ?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="6">توضیحات</th>
            <th colspan="1">تعداد کل</th>
            <th colspan="1">جمع قیمت (تومان)</th>
        </tr>
        <tr>
            <td colspan="6"></td>
            <td colspan="1"><?= $totalQuantity ?></td>
            <td colspan="1"><?= number_format($totalPrice) ?></td>
        </tr>
    </tfoot>
</table>
</body>
</html>