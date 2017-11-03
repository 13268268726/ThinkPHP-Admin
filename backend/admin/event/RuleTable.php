<?php
namespace backend\admin\event;

class RuleTable
{
    /**
     * 网站地图 等级展示
     * @param array $rule_data
     * @param array $group_data
     * @return string
     */
    public function init($rule_data, $group_data)
    {
        $str = '<table id="simple-table" class="table table-striped table-bordered table-hover table-condensed"><tbody>';
        foreach ($rule_data as $k1 => $v1) {
            // 选中标记
            $checked = '';
            if (in_array($v1['id'], $group_data['rules'])) {
                $checked = ' checked="checked"';
            }

            // 内容
            if (empty($v1['_data'])) {
                // 没有子栏目
                $str .= '<tr class="b-group">
            <th width="10%">
                <label> ' . $v1['title'] . '<input type="checkbox" name="rule_ids[]" value="' . $v1['id'] . '" onclick="checkAll(this)"' . $checked . '/></label>
            </th>
            <td></td>
        </tr>';
            } else {
                // 有子栏目
                $str .= '<tr class="b-group">
            <th width="10%">
                <label> ' . $v1['title'] . '
                    <input type="checkbox" name="rule_ids[]" value="' . $v1['id'] . '" onclick="checkAll(this)"' . $checked . '/>
                </label>
            </th>
            <td class="b-child">
            ' . $this->doWhile($v1['_data'], $group_data) . '
            </td>
        </tr>';
            }
        }
        $str .= '<tr>
            <td>&nbsp;</td>
            <td><input class="btn btn-success" type="submit" value="提交"/></td>
        </tr>
        </tbody></table>';
        return $str;
    }

    /**
     * 子循环体
     * @param array $data
     * @param array $group_data
     * @return string
     */
    public function doWhile($data, $group_data)
    {
        $str = '';
        foreach ($data as $k => $val) {

            // 选中标记
            $checked = '';
            if (in_array($val['id'], $group_data['rules'])) {
                $checked = ' checked="checked"';
            }

            // 循环体
            if (empty($val['_data'])) {
                $str .= '<label>&emsp;' . $val['title'];
                $str .= '<input type="checkbox" name="rule_ids[]" value="' . $val['id'] . '"' . $checked . '>';
                $str .= '</label>';
            } else {
                $str .= '<table class="table table-striped table-bordered table-hover table-condensed">
    <tr class="b-group">
        <th width="10%">
            <label>
                ' . $val['title'] . '
                <input type="checkbox" name="rule_ids[]" value="' . $val['id'] . '" onclick="checkAll(this)"' . $checked . '>
            </label>
        </th>
        <td>
            ' . $this->doWhile($val['_data'], $group_data) . '
        </td>
    </tr>
</table>';
            }
        }
        return $str;
    }
}
