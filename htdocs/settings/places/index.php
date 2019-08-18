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
        "check_callback": function (operation, node, node_parent, node_position, more) {
            if(operation==='rename_node') {
                params={
                    "id": node.id,
                    "text": node_position
                };
                $.get('/api/places/rename/', params, function(result) {
                    if(result.error) {
                        alert(result.error);
                        return false;
                    }
                    return true;
                });
            }
            if(operation==='move_node') {
                params={
                    "place_id": node.id,
                    "parent": node_parent.id
                };
                $.get('/api/places/move/', params, function(result) {
                    if(result.error) {
                        alert(result.error);
                        return false;
                    }
                    return true;
                });
            }
            if(operation==='delete_node') {
                params={
                    "id": node.id
                };
                $.get('/api/places/delete/', params, function(result) {
                    if(result.error) {
                        alert(result.error);
                        return false;
                    }
                    return true;
                });
            }
            return true;
        }
    },
    "contextmenu":{         
    "items": function($node) {
        var tree = $("#container").jstree(true);
        return {
            "Create": {
                "label": "Добавить элемент",
                "action": function (obj) { 
                    params={
                        "text": "Новый элемент",
                        "parent": $node.id
                    };
                    $.get('/api/places/create/', params, function(result) {
                        if(result.error) {
                            alert(result.error);
                            return;
                        }
                        $node = tree.create_node($node, result);
                        tree.edit($node);
                    });
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
    });
});
</script>
<?php
HTML::showPageFooter();