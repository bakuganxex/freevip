<div class="col-lg-9 order-is-first">
    <div class="block">
        <div class="block_head">Заявки на бесплатный VIP</div>
        <div class="table-responsive mb-0">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <td>Действие</td>
                        <td>Автор</td>
                        <td>Статус</td>
                        <td>Дата создания</td>
                    </tr>
                </thead>
                <tbody id="freevip_requests">
                    {requests}
                </tbody>
            </table>
        </div>
    </div>

    <div id="pagination2">{pagination}</div>
</div>

<div class="col-lg-3 order-is-last">
    {if(is_auth())}
    <div class="block">
        <a href="../freevip/add" class="btn btn-outline-primary btn-xl">Создать заявку</a>
    </div>
    {/if}

    <div class="block">
        <div class="block_head">
            Сервер
        </div>
        <div class="vertical-navigation">
            <ul>
                {servers}
            </ul>
        </div>
    </div>

    {if(is_auth())}
        {include file="/home/navigation.tpl"}
        {include file="/home/sidebar_secondary.tpl"}
    {else}
        {include file="/index/authorization.tpl"}
        {include file="/index/sidebar_secondary.tpl"}
    {/if}
</div> 
