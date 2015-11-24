<div>
    <div class="col-sm-3">
        <h3 class="text-center">Выберите день</h3>
        <div id="sandbox-container"></div>
            <a class="btn btn-success col-md-offset-2" href="<?php echo Helper::Action(add, task) ?>" role="button">Новая задача</a>
    </div>
        <div class="col-sm-9">
            <h3 class="text-center" id="taskto">Задачи</h3>
            <div id="results"><img src="image/724.GIF"/>
            </div>
        </div>
</div>


<script>
      $('#sandbox-container').datepicker({
              weekStart: 1,
              language: "ru",
              todayHighlight:true
      });
      var dp = $('#sandbox-container').datepicker();
      dp.on("changeDate",function(e){
          $('#results').html("<img src=\"image/724.GIF\"/>");
            $('#taskto').html("Задачи на "+e.date.getDate() + 
                    "." + (e.date.getMonth()+1) + 
                    "." + e.date.getFullYear());
         $.ajax({
          url: 'index.php?controller=task&action=taskList&time='+e.date.getTime(),
          success: function(data) {
            $('#results').html(data);
          }
        });
      });

      dp.datepicker('setDate', new Date());
</script>
 