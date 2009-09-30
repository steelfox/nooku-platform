<? /** $Id$ */ ?>
<? defined('KOOWA') or die('Restricted access'); ?>

<? @style(@$mediaurl.'/com_beer/css/grid.css'); ?>
<? @style(@$mediaurl.'/com_beer/css/beer_admin.css'); ?>

<? $attribs = array('class' => 'inputbox', 'size' => '1', 'onchange' => 'this.form.submit();');?>

<form action="<?= @route()?>" method="get" class="form-filters">
	<input type="hidden" name="option" value="com_beer" />
	<input type="hidden" name="view" value="people" />
	
	<div class="filter-search">
	<?= @text('Search'); ?>:
	<input name="search" id="search" value="<?= @$state->search;?>" />
	<button onclick="this.form.submit();"><?= @text('Go')?></button>
	<button onclick="document.getElementById('search').value='';this.form.submit();"><?= @text('Reset'); ?></button>
	</div>
	<?= @template('filter_name'); ?>

</form>

<form action="<?= @route()?>" method="post" name="adminForm" class="form-grid">
	<input type="hidden" name="id" value="" />
	<input type="hidden" name="action" value="" />
	<table class="adminlist" style="clear: both;">
		<thead>
			<tr>
				<th width="5">
					<?= @text('NUM'); ?>
				</th>
				<th width="20">
					<input type="checkbox" name="toggle" value="" onclick="checkAll(<?= count(@$people); ?>);" />
				</th>
				<th>
					<?= @helper('grid.sort', 'Name', 'firstname', @$state->direction, @$state->order); ?>
				</th>
				<th>
					<?= @helper('grid.sort', 'Department', 'department', @$state->direction, @$state->order); ?><br/>
					<?= @helper('admin::com.beer.helper.select.departments', @$state->beer_department_id, 'beer_department_id', $attribs, '', true) ?>
					
				</th>
				<th>
					<?= @helper('grid.sort', 'Office', 'office', @$state->direction, @$state->order); ?><br/>
					<?= @helper('admin::com.beer.helper.select.offices', @$state->beer_office_id, 'beer_office_id', $attribs, '', true) ?>
				</th>
				<th>
					<?= @helper('grid.sort', 'User', 'user_name', @$state->direction, @$state->order); ?>
				</th>
				<th>
					<?= @helper('grid.sort', 'Enabled', 'enabled', @$state->direction, @$state->order); ?><br/>
					<?= @helper('admin::com.beer.helper.select.enabled',  @$state->enabled ); ?>
				</th>
				<th>
					<?= @helper('grid.sort', 'ID', 'beer_person_id', @$state->direction, @$state->order); ?>
				</th>
			</tr>
		</thead>
		<tbody>
		
		<?= @template('form_items'); ?>
			
		<? if (!count(@$people)) : ?>
		<tr>
			<td colspan="8" align="center">
				<?= @text('No items found'); ?>
			</td>
		</tr>
		<? endif; ?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="20">
					<?= @helper('admin::com.beer.helper.paginator.pagination', @$total, @$state->offset, @$state->limit) ?>
				</td>
			</tr>
		</tfoot>
	</table>
</form>