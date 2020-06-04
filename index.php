<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Рейтинг студентов");?>
<?php
//подключаем bootstrap
use Bitrix\Main\Page\Asset;
Asset::getInstance()->addJs("https://code.jquery.com/jquery-3.0.0.js");
Asset::getInstance()->addJs("https://code.jquery.com/jquery-migrate-3.3.0.js");
Asset::getInstance()->addCss("https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css");
Asset::getInstance()->addJs("https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js");
Asset::getInstance()->addJs("https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js");
?>


<script>
    $(document).ready ( function() {
        var iddanger=0;
        $('.dropdown-item.success').click(function () {
            var scroll =$(window).scrollTop();
            var id = $(this).attr('id');
            $.post( "acceptefuse.php",{class:$(this).attr('class'), id:$(this).attr('id')}, function(data) {
                if (data=='success' ){
                    $('#'+id).html('<div class="btn-group btn-block"> <a class="btn btn-success disabled" href="#" disabled> Принято </a> </div>');
                    $(window).scrollTop(scroll);
                }
                if (data=='danger' ){
                    $('#'+id).html('<div class="btn-group btn-block"> <a class="btn btn-danger disabled" href="#" disabled> Отклонено </a> </div>');
                    $(window).scrollTop(scroll);
                }
            });
        });

        $('.btn.btn-primary.danger').click(function () {
            if ($.isEmptyObject($('#textarea').val())){
                 $('.form-control').addClass('is-invalid');
            }else {
                   $('.form-control').removeClass('is-invalid');
                  var scroll =$(window).scrollTop();
                   $.post( "acceptefuse.php",{class:'danger', id:iddanger, text:$('#textarea').val()}, function(data) {
                      var jsondata = JSON.parse(data);
                      if (jsondata['class']=='danger' ){
                          $('#'+iddanger).html('<div class="btn-group btn-block"> <a class="btn btn-danger disabled" href="#" disabled> Отклонено </a> </div>');
                          $(window).scrollTop(scroll);
                          $('#textarea').val('');
                          $('.text.'+iddanger).html(jsondata['text']);
                      };
                       $('#exampleModalCenter').modal('hide');
                });
            }
        });
        $('#exampleModalCenter').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget) // Button that triggered the modal
            iddanger =button.data('id');
            // var recipient = button.data('whatever') // Extract info from data-* attributes
            // // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
            // // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
            // var modal = $(this)
            // modal.find('.modal-title').text('New message to ' + recipient)
            // modal.find('.modal-body input').val(recipient)
        });
    });
</script>

<?php
Class StudRaiting
{
    public $StudRaiting;
    function __construct()
    {
        Global $DB;
        $results = $DB->query("SELECT * FROM studraiting");
        while($row=$results->fetch())
        {
            $this->StudRaiting[] =$row;
        }
    }

    function __destruct()
    {
        print "Уничтожение";
    }

    function show(){
    ?>    <!-- Modal -->
<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Причина отклонения</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
          </button>
      </div>
      <div class="modal-body">
          <textarea class="form-control" aria-label="With textarea" rows="10" id="textarea" placeholder="Обязательно к заполнению!"></textarea>
      </div>
      <div class="modal-footer">
          <button type="button" class="btn btn-primary danger">Добавить комментарий</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div> <?php
        echo '<table class="table table-hover">';
            echo '<thead>';
                echo '<tr>';
                    echo '<th style="width: 5%">cstud</th>';
                    echo '<th style="width: 40%">csection</th>';
                    echo '<th style="width: 10%">data</th>';
                    echo '<th style="width: 20%">files_name</th>';
                    echo '<th style="width: 10%">cstatus</th>';
                    echo '<th style="width: 15%">note</th>';
                echo '</tr>';
            echo '</thead>';

            echo '<tbody>';
            foreach ($this->StudRaiting as $row) {
                echo '<tr>';
                    echo '<td>';
                        echo $row['cstud'];
                    echo '</td>';
                    echo '<td>';
                        $this->show_csection($row['csection']);
                    echo '</td>';
                    echo '<td>';
                        echo $row['date_load'];
                    echo '</td>';
                    echo '<td>';
                        $files=$row['files'];
                        $files_name=$row['files_name'];
                        echo "<a href=$files> $files_name</a>";
                    echo '</td>';
                echo '<td>';
                $id =$row['id'];
                switch ($row['cstatus']){
                    case '1':
                        echo "<div class='btn-group btn-block' id=$id>
                                          <a class='btn btn-primary dropdown-toggle' href='#' role='button' id='dropdownMenuLink' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                                              Загружено
                                          </a>
                                          <div class='dropdown-menu' aria-labelledby='dropdownMenuLink'>
                                               <a class='dropdown-item success' href='#' id=$id >Принято</a>
                                               <button type='button' data-toggle='modal' data-target='#exampleModalCenter' class='dropdown-item danger' href='#'  data-id=$id>Отклонено</button>
                                          </div>
                                    </div>";
                        break;
                    case '2':
                        echo '<div class="btn-group btn-block">
                                          <a class="btn btn-success disabled" href="#" disabled>
                                              Принято
                                          </a>          
                                    </div>';
                        break;
                    default:
                        echo '<div class="btn-group btn-block">
                                          <a class="btn btn-danger disabled" href="#" disabled>
                                              Отклонено
                                          </a>          
                                    </div>';
                }
                echo '</td>';
                    echo '<td>';
                        echo "<div class='comment'><p class='text $id'></p></div>";
                    echo '</td>';
                echo '</tr>';
            }
            echo '</tbody>';
        echo '</table>';
    }

    function show_csection($csection){
            Global $DB;
            $reslut ='';
            $parrents_id=$csection;
            while($parrents_id) {
                $results = $DB->query("SELECT * FROM catsection WHERE id =$parrents_id");
                while ($row = $results->fetch()) {
                    $parrents_id = $row['parents_id'];
                    if ($reslut==''){
                        $reslut=$row['name'];
                    } else {
                        $reslut = $row['name'] . ' --> ' . $reslut;
                    }
                }
            }
             print_r($reslut);
    }
};
?>
<?
    $Progect = new StudRaiting();
    $Progect->show();

?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
