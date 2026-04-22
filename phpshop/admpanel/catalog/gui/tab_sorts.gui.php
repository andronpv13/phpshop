<?php

function tab_sorts($data) {
    global $SysValue,$link_db;
    
    $sort=unserialize($data['sort']);
    $dis = null;
    $sql = "select * from " . $SysValue['base']['sort_categories'] . " where category=0 order by num";
    $result = mysqli_query($link_db,$sql);
    while ($row = mysqli_fetch_array($result)) {
        $id = $row['id'];
        $name = $row['name'];
        $sel = "";
        if (is_array($sort))
            foreach ($sort as $v) {
                if ($id == $v)
                    $sel = "selected";
            }
        $dis.='
	<optgroup label="'.$name.'">
	'. tab_sorts_val($id, $sort) . '
	</optgroup>
	';
    }
    $disp = '<select name=sort_new[] class="selectpicker show-menu-arrow" data-live-search="true" data-container=""  data-style="btn btn-default btn-sm" data-width="99%" data-size="auto"  multiple>'.$dis.'</select>';
    return $disp;
}

function tab_sorts_val($n, $sort) {
    global $SysValue,$link_db;
    $dis=null;
    $sql = "select * from " . $SysValue['base']['sort_categories'] . " where category=$n order by num";
    $result = mysqli_query($link_db,$sql);
    while ($row = mysqli_fetch_array($result)) {
        $id = $row['id'];
        $name = substr($row['name'], 0, 100);
        $sel = "";
        
        if (is_array($sort))
            foreach ($sort as $v) {
                if ($id == $v)
                    $sel = "selected";
            }
            
        if(!empty($row['filtr']))
            $row['description'].= __(' Фильтр');
        
        if(!empty($row['virtual']))
            $row['description'].= __(' Каталог');
            
        $dis.="<option value=" . $id . " " . $sel . " data-subtext=\"".$row['description']."\">" . $name . "</option>\n";
    }
    return $dis;
}

function tab_parent($data) {
    global $SysValue,$link_db;
    
    $dis = '<option value="0">'.__('Не выбрано').'</option>';
    $sql = "select * from " . $SysValue['base']['parent_name'] . " where enabled='1' order by name ";
    $result = mysqli_query($link_db,$sql);
    while ($row = mysqli_fetch_array($result)) {

        if ($row['id'] == $data['parent_title'])
            $sel = "selected";
        else  $sel = null;
        
        if(!empty($row['color']))
            $row['name'].=' + '.$row['color'];
        
        $dis.="<option value=" . $row['id'] . " " . $sel . ">" . $row['name']. "</option>\n";
            
    }
    $disp = '<select name="parent_title_new" class="selectpicker show-menu-arrow" data-container=""  data-style="btn btn-default btn-sm" data-width="auto" data-size="auto">'.$dis.'</select>';
    return $disp;
}
?>