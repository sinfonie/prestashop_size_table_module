{*
*
* ScsForm
* @author <sinfonie@o2.pl>
*
*}

<div class="panel product-tab" id="sinclothessizing">
  <h3 class="tab">{l s='Add size table' mod='sinclothessizing'}</h3>
  <div class="form-group">
    <label class="control-label col-lg-3" for="choose-model">
      {l s='Choose model' mod='sinclothessizing'}
    </label>
    <div class="col-lg-9">
      {foreach from=$model_data key=id item=model}
        {if $model.active}
          <div class="radio">
            <label for="simple_product">
              <input type="radio" name="choose-model" id="{$id}" value="0">
              {$model.name}</label>
          </div>
        {/if}
      {/foreach}
    </div>
  </div>
  <div class="form-group">

    {foreach from=$model_data key=model_id item=model}
      {if $model.active}
        <div id="scs-panel-{$model_id}" class="panel col-lg-12">
          <div class="table-responsive clearfix">
            <table id="table" class="table table-condensed">
              <thead>
                <tr>
                  <th colspan="{count($model.properties)}">
                    <div><strong>{$model.name}</strong></div>
                  </th>
                </tr>
                <tr>
                  {foreach from=$model.properties key=prop_id item=prop_name}
                    <th>
                      <div class="text-center ">{$prop_name}</div>
                    </th>
                  {/foreach}
                </tr>
              </thead>
              <tbody>
                {foreach from=$model.dimensions key=dim_id item=dimension}
                  <tr>
                    {foreach from=$dimension key=prop_id item=property_value}
                      <td>
                        <div class="input-group">
                          <span class="input-group-addon col-1">{$model.attributes[$dim_id]}</span>
                          <input value="{$property_value}" class="form-control" type="number"
                            id="input_{$model_id}_{$dim_id}_{$prop_id}" maxlength="3"
                            name="scs_{$model_id}_{$dim_id}_{$prop_id}">
                        </div>
                      </td>
                    {/foreach}
                  </tr>
                {/foreach}
              </tbody>
            </table>
          </div>
        </div>
      {/if}
    {/foreach}

  </div>
  <div class="panel-footer">
    <a href="{$link->getAdminLink('AdminProducts')|escape:'html':'UTF-8'}{if isset($smarty.request.page) && $smarty.request.page > 1}
     submitFilterproduct={$smarty.request.page|intval}{/if}" class="btn btn-default"><i
        class="process-icon-cancel"></i> {l s='Cancel' mod='sinclothessizing'}</a>
    <button type="submit" name="submitAddproduct" class="btn btn-default pull-right" disabled="disabled"><i
        class="process-icon-loading"></i> {l s='Save' mod='sinclothessizing'}</button>
    <button type="submit" name="submitAddproductAndStay" class="btn btn-default pull-right" disabled="disabled"><i
        class="process-icon-loading"></i> {l s='Save and stay' mod='sinclothessizing'}</button>
  </div>
</div>