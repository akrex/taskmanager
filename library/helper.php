<?php
//класс для удобного формирования данных
class Helper{
    
    //при начале формы запоминаем ее контекст
    private static $currentFormContext;
    
    public static function AddCss($path){
        echo '<link rel="stylesheet" type="text/css" href="'.$path.'">';
    }
    
    public static function AddJavaScript($path){
        echo '<script type="text/javascript" src="'.$path.'"></script>';
    }
    
    public static function ActionLink($display,$action,$controller, 
            $parameters = array(), $attributes = ""){
        $link = Helper::Action($action, $controller, $parameters);
        echo '<a href="'.$link.'" '.$attributes.' >'.$display.'</a>';
    }
    
    public static function Action($action,$controller,$parameters = array()){
        $res = "index.php?";
        $dc = TRUE;
        if($controller != RouteManager::$defaultcontroller){
            $res .= "controller=".$controller;
            $dc = FALSE;
        }
        if($action != RouteManager::$defaultaction){
            //если имя контроллера не стандартное, то добавляем &
            if($dc==FALSE){
                $res .="&";
            }
            $res .="action=".$action;
        }
        $res .= Helper::ParametersToString($parameters);
        return $res;
    }
    
    private static function ParametersToString($parameters){
        $res = "";
        if($parameters != null){
            foreach($parameters as $key => $value){
                $res .= "&".$key."=";
                $res .= $value;
            }
        }
        return $res;
    }
    
    private static function HtmlParametersToString($parameters){
        $res = "";
        if($parameters != null){
            foreach($parameters as $key => $value){
                $res .= " ".$key."=";
                $res .= "\"".$value."\"";
            }
        }
        return $res;
    }
    
    public static function BeginForm($context, $action, $controller, $parameters = array()){
        Helper::$currentFormContext = $context;
        $res = '<form class="form-horizontal" action="index.php?controller='.$controller.'&action='.$action.'" method="POST"';
        $res .= Helper::HtmlParametersToString($parameters);
        $res .= '>';
        echo $res;
    }
    
    public static function EndForm(){
        Helper::$currentFormContext = null;
        echo '</form>';
    }
    
    public static function InputFor($name, $parameters){
        echo Helper::GenerateInput($name, $parameters, "input");
    }
    
    public static function PasswordFor($name, $parameters){
        echo Helper::GenerateInput($name, $parameters, "password");
    }
    
    public static function HiddenFor($name, $parameters = null){
        echo Helper::GenerateInput($name, $parameters, "hidden");
    }
    
    public static function CheckboxFor($name, $parameters){
        if(isset(Helper::$currentFormContext[$name])){
            $parameters['checked'] = "checked";
        }
        echo Helper::GenerateInput($name, $parameters, "checkbox");
    }
    
    private static function GenerateInput($name,$parameters,$type){
        //проверяем есть ли ошибка для элемента
        $outclass = "form-group";
        if(Helper::$currentFormContext["error"][$name]){
            $outclass .= " has-error";
        }
        $res = '<div class="'.$outclass.'">';
        if($type != "hidden"){
            $res .= '<label class="col-sm-2 control-label" for="'.$name.'">'.Helper::$currentFormContext["text"][$name].'</label>';
        }
        $res .= '<div class="col-sm-10">';
        $res .= '<input type="'.$type.'" name="'.$name.'" value="'.Helper::$currentFormContext[$name].'" ';
        $res .= Helper::HtmlParametersToString($parameters);
        $res .= ' />';
        if(Helper::$currentFormContext["error"][$name]){
            $res .= '<div style="color:red">*'.Helper::$currentFormContext["error"][$name].'</div>';
        }
        $res .= '</div></div>';
        return $res;
    }
    
    public static function DateTimeLinkedFor($name1, $name2, $parameters1, $parameters2){
        $res = Helper::GenerateDateTime($name1, $parameters1);
        $res .= Helper::GenerateDateTime($name2, $parameters2);
        $res .= "<script type=\"text/javascript\">
            $(function () {
                $('#".$name1."').datetimepicker({
                        stepping:5,
                        sideBySide:true,
                        allowInputToggle:true,
                        locale: moment.locale('ru')});
                $('#".$name2."').datetimepicker({
                    useCurrent: false, //Important! See issue #1075
                    stepping:5,
                    sideBySide:true,
                    allowInputToggle:true,
                    locale: moment.locale('ru')
                });
                $(\"#".$name1."\").on(\"dp.change\", function (e) {
                    $('#".$name2."').data(\"DateTimePicker\").minDate(e.date);
                });
                $(\"#".$name2."\").on(\"dp.change\", function (e) {
                    $('#".$name1."').data(\"DateTimePicker\").maxDate(e.date);
                });
            });
        </script>";
        echo $res;
    }
    
    private static function GenerateDateTime($name, $parameters){
        $res = '<div class="form-group">';
        $res .= '<label class="col-sm-2 control-label" for="'.$name.'">'.Helper::$currentFormContext["text"][$name].'</label>';
        $res .= '<div class="col-sm-10">';
        $res .= "<div class='input-group date' id='".$name. "'";
        $res .= Helper::HtmlParametersToString($parameters);
        $res .= ' />';
        $res .= '<input  name="'.$name.'" type=\'text\' class="form-control" value="'.Helper::$currentFormContext[$name].'"/>
                      <span class="input-group-addon">
                          <span class="glyphicon glyphicon-calendar"></span>
                      </span>
              </div>';
        
        if(Helper::$currentFormContext["error"][$name]){
            $res .= '<div style="color:red">*'.Helper::$currentFormContext["error"][$name].'</div>';
        }        
        $res .= '</div></div>';
        return $res;
    }
    
    public static function Submit($text, $class = null){
        if($class == null){
            $class = "btn-default";
        }
        $res = '<div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
              <button type="submit" class="btn '.$class.'">'.$text.'</button>
            </div>
          </div>';
        echo $res;
    }
}