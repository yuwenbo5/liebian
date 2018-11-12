/**
 * table_list列表的js操作
 * @since 2018-11-07
 */
$(function(){
    $('table tr.table_list').on('click',function(){
        var _that = $(this).find('td').eq(0).find("input[type='checkbox']");
        var checked = _that.prop('checked');
        if(checked){
            _that.attr('checked',false);
        }else{
            _that.attr('checked',true);
        }
    });
});