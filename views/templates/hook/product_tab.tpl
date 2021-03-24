{*
*
* ScsForm
* @author <sinfonie@o2.pl>
*
*}

<div class="panel product-tab">
  <h3>Add new model</h3>
  <div class="form-group">
    <label class="control-label col-lg-2" for="add-new-model">
      Choose model
    </label>
    <div class="col-lg-3">
      <select name="add-new-model" id="add-new-model">
        {foreach from=$model_data key=id item=value}
          <option value="{$id}">{$value.name}</option>
        {/foreach}
      </select>
    </div>


    {foreach from=$model_data key=model_id item=model}
      <div class="panel col-lg-12">
        <div class="table-responsive  clearfix">
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
              {foreach from=$model.dim key=dim_id item=dim_name}
                <tr>
                  {foreach from=$model.properties key=prop_id item=prop_name}
                    <td>
                      <div class="input-group">
                        <span class="input-group-addon col-1">{$dim_name}</span>
                        <input class="form-control" type="number" id="input_{$model_id}_{$dim_id}_{$prop_id}" maxlength="3"
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



<div class="panel product-tab">
  <h3>Lista Utworoznych modeli</h3>
  <div class="form-group">
    Wybrany model
    <input type="radio">
    <input type="radio" value="true">
    <input type="radio">
  </div>
  <div class="form-group">
    Tabela wyświetlana
  </div>
</div>


</div>