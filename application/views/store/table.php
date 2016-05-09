<!-- 表格 -->
<?= form_open('export/run/');?>
<div class="container-fluid" id='table'>
	<div class="row-fluid">
		<div class="span12">
			<table class="table table-bordered table-hover table-striped">
				<thead>
					<tr>
						<th class='no-print'>
							导出
						</th>
						<th>
							购买时间
						</th>
						<th>
							顾客姓名
						</th>
						<th>
							顾客电话
						</th>
						<th>
							品牌型号
						</th>
            <th>
              数码类型
            </th>
            <th>
              串码
            </th>
            <th>
              位置
            </th>
            <th class='no-print'>
              操作
            </th>
					</tr>
				</thead>
				<tbody>
          <?php foreach($table as $val): ?>
						<td class='no-print'>
              <p class='p-checkbox'><input class='checkbox' type="checkbox" id="checkbox1" name='export[]' value='<?=$val['id']?>'></p>
						</td>
						<td>
              <?= $val['buy_date']; ?>
						</td>

						<td>
              <?= $val['customer_name']; ?>
						</td>

            <td>
              <?= $val['customer_phone']; ?>
						</td>

            <td>
              <?= $val['brand'].$val['types']; ?>
						</td>

            <td>
              <?= $val['value']; ?>
						</td>
            
            <td>
              <?= $val['string_code']; ?>
						</td>

            <td>
              <?= $val['state_msg']; ?>
						</td>

            <td class='no-print'>
              <a href="<?= base_url('index.php/details/show/'.$val['id'].'/store') ?>" class="btn btn-info btn-xs active" role="button">查看详细</a>
              <?= empty($val['new_string'])?'':'<span class="label label-warning">(新串码)</span>'; ?>
              <?= empty($val['offer'])?'':'<span class="label label-warning">(报价)</span>'; ?>
              <?= $val['state'] !== '8'?'':'<span class="label label-warning">(退仓)</span>'; ?>
              <a href="<?=base_url('index.php/store/del/'.$val['id'])?>" class="btn btn-danger btn-xs actiae right glyphicon glyphicon-trash" role="button"></a>
						</td>

					</tr>
          <?php endforeach; ?>
          <tr class='no-print'><td class='select-all'>全选</td></tr>
				</tbody>
			</table>
		</div>
	</div>
</div>
<div class='btn btn-default print-table'>
<input type='submit' class='toExcel' target="_self" name='submit' value='导出表格'>
</div>

<div class='btn btn-default print-table'>
  <p class='print-button' onclick="jQuery('#table').print()" target="_self">打印表格</p>
</div>
</form>

<?= $page;?>
