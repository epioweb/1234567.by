<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Оплата и доставка");
?><div class="payment_page">
    <h3>Оплата и доставка</h3>
    <ul>
        <li>- Доставка по всей РБ.</li>
        <li>- Минимальная сумма заказа 100 000 BYR.</li>
    </ul>
    <div class="item">
        <div class="image"><img src="/i/payment_img1.jpg" /></div>
        <ul>
            <li><strong>Минск:</strong></li>
          <li>- Товары доставляются на следующий день после оформления заказа. </li>
          <li>- Доставка бесплатная при заказе на сумму от 500 000, до 500 000 доставка платная 50 000.</li>
          <li>- Доставка осуществляется в ПН-ПТ, с 11:00 до 22:00.</li>
        </ul>
    </div><!--/item-->
    <div class="item">
        <div class="image"><img src="/i/payment_img2.jpg" /></div>
        <ul>
            <li><strong>Регионы:</strong></li>
            <li>- Доставка осуществляется «наложенным платежом» по почте.</li>
            <li>- Срок доставки почтой 2-5 дней. </li>
            <li>- Стоимость товара = розничная цена товара + стоимость услуг почты.</li>
            <li>- Посылки отправляются 2 раза в неделю: во вторник и пятницу.</li>
        </ul>
    </div><!--/item-->
    <ul>
        <li><strong>Документы, подтверждающие факт приобретения товара:</strong> </li>
        <li>» <a href="/upload/docs/cashdesk_invoice.jpg">Кассовый чек</a> </li>
        <li>» <a href="/upload/docs/noncashdesk_invoice.jpg">Квитанция о приеме наличных денежных средств</a> </li>
    </ul>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>