<h2>Вход</h2>
<?php Helper::BeginForm($this->data, "login","profile", array("role" => "form")); ?>
    <?php Helper::InputFor("username", array("class"=>"form-control", "placeholder"=>"Введите имя пользователя")); ?>
    <?php Helper::PasswordFor("password", array("class"=>"form-control", "placeholder"=>"Введите пароль")); ?>
    <div class="form-group col-sm-4">
        <p>У вас еще нет аккаунта? Тогда 
        <?php Helper::ActionLink("зарегестритуйтесь","register","profile"); ?>.</p>
        <p>Возможно вы <?php Helper::ActionLink("забыли пароль","restore","profile"); ?>?</p>
    </div>    
    <?php Helper::Submit("Войти"); ?>
<?php Helper::EndForm(); ?>