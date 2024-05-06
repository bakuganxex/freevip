<div class="col-lg-9 order-is-first"> 
    <div class="block blockRequestsAbout">
        <div class="headerBold">
            Бесплатный <span class="vipName">VIP</span> для девушек
        </div>

        <hr class="hr-dashed">
 
        <p>
            Администрация проекта, как истинные джентльмены, решили порадовать женскую половину нашего проекта бесплатными VIP привилегиями.
        </p>

        <p>
            [Акция] была создана для того, что бы привлечь прекрасный женский пол на наш игровой сервер.
        </p>
<!-- 
        <p>
            Для получения VIP привилегий на нашем сервере, Вам необходимо ознакомиться  <a href="https://adekvatserver.ru/pages/rules_public" class="rulesLink">"Правилами сервера"</a>, иметь в наличии микрофон (обязательно) для общения в игре, вы можете оставить заявку в данной теме.
        </p> -->

        <div class="wrapperUl">
            <div class="miniHeader">
                Обязательные критерии
            </div>

            <ul>
                <li>
                    Вы должны быть девушкой, как бы странно это не звучало.
                </li>
    
                <li>
                    Вам должно быть минимум 16 лет.
                </li>
    
                <li>
                    Наличие микрофона.<br><span class="always">*</span> Обязательно общаться по микрофону на сервере.
                </li>
    
                <li>
                    Прикрепить сигну<br><span class="always">*</span> На сигне должно быть видно ваше лицо.<br><span class="always">*</span> На бумаге должны быть следующие надписи: название проекта, название сервера, айпи сервера.
                </li>
            </ul>
        </div>
        <br>
        Срок рассмотрения заявки до 3-х дней.
    </div>

    <div class="block blockRequestsAdd">
        <!-- <div class="block_head">Создание заявки на VIP для девушек</div> -->

        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="server">
                        Выберите сервер
                    </label>

                    <select class="form-control" id="server">
                        {servers_options}
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="real_name">
                        Имя
                    </label>
        
                    <input class="form-control" type="text" id="real_name" maxlength="30" placeholder="Укажите своё настоящее имя" <?php if(!empty($user->name)) {
                        echo "value=\"" . $user->name . "\"";
                    } ?>>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="real_age">
                        Возраст 
                        <!-- <small>
                            (Минимум - 16 лет)
                        </small> -->
                    </label>
        
                    <input class="form-control" type="number" id="real_age" placeholder="Укажите реальный возраст" min="16" max="96">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="game_name">
                        Игровой ник
                    </label>
        
                    <input class="form-control" type="text" id="game_name" maxlength="40" placeholder="Укажите свой игровой ник" <?php if(!empty($user->nick)) {
                        echo "value=\"" . $user->nick . "\"";
                    } ?>>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="soc_vk">
                        Профиль VK
                    </label>

                    <input class="form-control" type="text" id="soc_vk" maxlength="60" placeholder="Укажите ссылку на профиль VK" <?php if(!empty($user->vk_api)) {
                        echo "value=\"vk.com/id" . $user->vk_api . "\"";
                    } ?>>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="have_mic">
                        Микрофон
                    </label>
        
                    <select class="form-control" id="have_mic">
                        <option value="1">
                            Имеется
                        </option>
 
                        <option value="0">
                            Отсутствует
                        </option>
                    </select>
                </div>    
            </div>

<!--                         <button class="selectFileBtn">
                            Выберите файл
                        </button> -->
        </div>

        <script>
            $('#signa').on('change', function(e) {
                let file = this.files[0]; 
	            $('.selectedFile').html(file.name);
            });
        </script>

        <div id="result"></div> 

        <button type="button" class="btn btn-default addRequest" onclick="create_freevip_request();">
            {if(is_auth())}
            Создать заявку
            {else}
            Авторизуйтесь на сайте
            {/if}
        </button>
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
