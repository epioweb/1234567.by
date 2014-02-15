<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="consultant">
    <h4>Онлайн-консультант</h4>
    <p>
        <a class="jivo-btn jivo-online-btn jivo-btn-light" style="cursor: pointer;" onclick="jivo_api.open();">В сети</a>
        <a class="jivo-btn jivo-offline-btn jivo-btn-light" style="cursor: pointer; display: none;" onclick="jivo_api.open();" >Не в сети</a>
            <?/*if(checkWebimOnline()):?>
                <a title="Online-консультант" onclick="window.open('/webim/client.php','_blank', 'width=576,height=402,resizable=no,scrollbars=no,status=no');" href="#">
                     В сети
                </a>
            <?else:?>
                <a title="Online-консультант" onclick="window.open('/webim/client.php','_blank', 'width=576,height=402,resizable=no,scrollbars=no,status=no');" href="#">
                     Не в сети
                </a>
            <?endif?>
        <?endif*/?>
    </p>
</div><!--/consultant-->