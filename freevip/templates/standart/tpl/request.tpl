<div class="col-lg-9 order-is-first"> 
    <div class="row">
        <div class="col-md-8">
            <div class="block">
                <div class="block_head">
                    Информация
                </div> 
                
                <div class="blockTable">
                    <div class="blockTable_Column">
                        <div class="label">
                            Профиль на сайте
                        </div>

                        <div class="value miniProfile">
                            <a target="_blank" href="../profile?id={author}">
                                {login}
                            </a>
                        </div>
                    </div>

                    <div class="blockTable_Column">
                        <div class="label">
                            Сервер
                        </div>

                        <div class="value serverNameValue">
                            {server_name}
                        </div>
                    </div>

                    <div class="blockTable_Column">
                        <div class="label">
                            Имя
                        </div>

                        <div class="value">
                            {real_name}
                        </div>
                    </div>

                    <div class="blockTable_Column">
                        <div class="label">
                            Возраст
                        </div>
                        
                        <div class="value">
                            {real_age}
                        </div>
                    </div>

                    <div class="blockTable_Column">
                        <div class="label">
                            Игровой ник
                        </div>

                        <div class="value">
                            {game_name}
                        </div>
                    </div>

                    <div class="blockTable_Column">
                        <div class="label">
                            Игровое время
                        </div>

                        <div class="value">
                            {game_time}
                        </div>
                    </div>

                    <div class="blockTable_Column">
                        <div class="label">
                            VK
                        </div>

                        <div class="value">
                            <a href="{soc_vk_link}" target="_blank">
                                {soc_vk}
                            </a>
                        </div>
                    </div>

                    <div class="blockTable_Column">
                        <div class="label">
                            Статус
                        </div>

                        <div class="value">
                            <span class="text-{color}">
                                {status}
                            </span>
                        </div>
                    </div>

                    <div class="blockTable_Column">
                        <div class="label">
                            Дата создания
                        </div>

                        <div class="value">
                            {created_at}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if(isAccessed($module)) { ?>
        <div class="col-md-8">
            <div class="block">
                <div class="block_head">
                    Операции
                </div>

                <div class="buttonsWithOp">
                    <button class="acceptBtn" onclick="requestChangeStatus({request_id}, 1);">
                        Одобрить
                    </button>
 
                    <button class="rejectBtn" onclick="requestChangeStatus({request_id}, 2);">
                        Отклонить
                    </button>

                    <button class="deleteBtn" onclick="requestDelete({request_id});">
                        Удалить
                    </button>
                </div>
            </div>
        </div>
        <? } ?>

        <div class="col-md-12">
            <div class="block">
                <div class="block_head">
                    Комментарии
                </div>

                {if(is_auth())}
                <script src="../modules/editors/tinymce/tinymce.min.js"></script>

                <div id="add_new_comments">
                    <textarea id="text" maxlength="500"></textarea>
    
                    <div class="smile_input_forum mt-3">
                        <input id="send_btn" class="btn btn-primary" type="button" onclick="send_request_comment({request_id});" value="Отправить">
                        <div id="smile_btn" class="smile_btn" data-container="body" data-toggle="popover" data-placement="top" data-content="empty"></div>
                    </div>
                </div>
                <script>
                    $(document).ready(function() {
                        init_tinymce("text", "lite", "{file_manager_theme}", "{file_manager}", "{{md5($conf->code)}}");
                        get_smiles('#smile_btn', 1);
                    });
    
                    $('#smile_btn').popover({ html: true, animation: true, trigger: "click" });
                    $('#smile_btn').on('show.bs.popover', function () {
                        $(document).mouseup(function (e) {
                            var container = $(".popover-body");
                            if (container.has(e.target).length === 0){
                                $('#smile_btn').popover('hide');
                                selected = 'gcms_smiles';
                            }
                        });
                    });
    
                    function set_smile(elem){
                        var smile =  "<img src=\""+$(elem).attr("src")+"\" class=\"g_smile\" height=\"20px\" width=\"20px\">";
                        tinymce.activeEditor.insertContent(smile);
                        $('#smile_btn').popover('hide');
                        selected = 'gcms_smiles';
                    }
                </script>
                {/if}
                <div id="comments" class="mt-3">
                    <div class="loader"></div>
                    <script>load_request_comments({request_id});</script>
                </div>
            </div>
        </div>
    </div>
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
