<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-frenet" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
      </div>
      <div class="panel-body">
      	<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-frenet" class="form-horizontal">
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-postcode"><?php echo $entry_postcode; ?></label>
            <div class="col-sm-10">
              <input type="text" name="frenet_postcode" value="<?php echo $frenet_postcode; ?>" placeholder="<?php echo $entry_postcode; ?>" id="input-postcode" class="form-control" />
              <?php if ($error_postcode) { ?>
              <div class="text-danger"><?php echo $error_postcode; ?></div>
              <?php } ?>
            </div>
          </div>
            <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-msg_prazo"><?php echo $entry_msg_prazo; ?></label>
                <div class="col-sm-10">
                    <input type="text" name="frenet_msg_prazo" value="<?php echo $frenet_msg_prazo; ?>" placeholder="<?php echo $help_msg_prazo; ?>" id="input-msg_prazo" class="form-control" />
                </div>
            </div>
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-contrato-codigo"><span data-toggle="tooltip" title="<?php echo $help_frenet_key; ?>"><?php echo $entry_frenet_key; ?></span></label>
            <div class="col-sm-10">
              <div class="row">
                <div class="col-sm-4">
                  <input type="text" name="frenet_contrato_codigo" value="<?php echo $frenet_contrato_codigo; ?>" placeholder="<?php echo $entry_frenet_key_codigo; ?>" id="input-contrato-codigo" class="form-control" />
                </div>
                <div class="col-sm-4">
                  <input type="text" name="frenet_contrato_senha" value="<?php echo $frenet_contrato_senha; ?>" placeholder="<?php echo $entry_frenet_key_senha; ?>" id="input-contrato-senha" class="form-control" />
                </div>
                <div class="col-sm-4">
                </div>
              </div>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
            <div class="col-sm-10">
              <select name="frenet_status" id="input-status" class="form-control">
                <?php if ($frenet_status) { ?>
                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                <option value="0"><?php echo $text_disabled; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $text_enabled; ?></option>
                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-sort-order"><?php echo $entry_sort_order; ?></label>
            <div class="col-sm-10">
              <input type="text" name="frenet_sort_order" value="<?php echo $frenet_sort_order; ?>" placeholder="<?php echo $entry_sort_order; ?>" id="input-sort-order" class="form-control" />
            </div>
          </div>          
 		</form>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>
