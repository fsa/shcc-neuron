<?php

require_once '../../common.php';
Auth\Internal::grantAccess(['admin']);
HTML::addHeader('<script src="/libs/jstree/jstree.min.js"></script>');
HTML::addHeader('<link rel="stylesheet" href="/libs/jstree/style.min.css">');
HTML::showPageHeader('Объекты');
?>
<p>Создайте иерархию объектов - мест размещения устройств. Рекомендуется использовать древовидную структуру где корневыми элементами являются отдельные объекты недвижимости (дом, гараж, баня и т.д.), далее, у каждого объекта есть свои помещения (прихожая, спальня, кухня, подвал и т.д.). В каждом помещении могут иметься окна, двери и т.д. На окнах могут быть добавлены форточки, ставни и т.д.</p>
<p><a href="edit/">Создать новый элемент</a></p>
<div id="container"></div>
<script>
$(function() {
    $('#container').jstree({
    'core' : {
      'data' : {
        "url" : "/api/places/"
      },
      "check_callback": true
    },
    "contextmenu":{         
    "items": function($node) {
        var tree = $("#container").jstree(true);
        return {
            "Create": {
                "label": "Добавить элемент",
                "action": function (obj) { 
                    $node = tree.create_node($node, {"text": "Новый элемент"});
                    tree.edit($node);
                }
            },
            "Rename": {
                "label": "Переименовать",
                "action": function (obj) { 
                    tree.edit($node);
                }
            },                         
            "Remove": {
                "label": "Удалить",
                "action": function (obj) { 
                    tree.delete_node($node);
                    }
                }
            };
        }
    },

    "state" : { "key" : "places_state" },
    "plugins" : ["state", "dnd", "contextmenu"]
    })
        .on("move_node.jstree", function (e, data) {
            console.log(data);
            params={
                "place_id": data.node.id,
                "parent": data.parent
            };
            $.get('/api/places/move/', params, function(result) {
                if(result.error) {
                    alert('Не удалось переместить элемент');
                }

            });
        })
        .on("create_node.jstree", function (e, data) {
            console.log(data);
            params={
                "text": data.node.text,
                "parent": data.parent
            };
            $.get('/api/places/create/', params, function(result) {
                if(result.error) {
                    alert('Не удалось создать элемент');
                } else {
                    // Элементу нужно присвоить ID
                    console.log(e);
                }
            });
        })
            .on("delete_node.jstree", function (e, data) {
            params={
                "id": data.node.id
            };
            $.get('/api/places/delete/', params, function(result) {
                if(result.error) {
                    alert('Не удалось удалить элемент');
                }
            });
        })
            .on("rename_node.jstree", function (e, data) {
            params={
                "id": data.node.id,
                "text": data.node.text
            };
            $.get('/api/places/rename/', params, function(result) {
                if(result.error) {
                    alert('Не удалось переименовать элемент');
                }
            });
        });
});
</script>
<?php
HTML::showPageFooter();