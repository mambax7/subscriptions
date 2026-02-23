<div class="subscriptions-admin">
  <h1 class="xm-admin-title"><{$smarty.const._AM_SUBSCRIPTIONS_MODULE_LIST}></h1>
  <div class="xm-toolbar">
    <a href="modules.php?op=edit" class="xm-btn xm-btn--sm xm-btn--primary"><{$smarty.const._AM_SUBSCRIPTIONS_MODULE_ADD}></a>
  </div>
  <p class="xm-admin-desc">
    Connected modules allow other XOOPS modules to gate their content behind Subscriptions plans.
    Register a module here and copy the API key into your third-party module's configuration.
  </p>

  <{if $xm_modules}>
  <div class="xm-table-wrap">
    <table class="xm-table xm-sortable" role="table">
      <thead>
        <tr>
          <th scope="col">ID</th>
          <th scope="col"><{$smarty.const._AM_SUBSCRIPTIONS_MODULE_DIRNAME}></th>
          <th scope="col"><{$smarty.const._AM_SUBSCRIPTIONS_MODULE_NAME}></th>
          <th scope="col">Access Rules</th>
          <th scope="col">Active</th>
          <th scope="col" class="sorter-false"><{$smarty.const._AM_SUBSCRIPTIONS_ACTIONS}></th>
        </tr>
      </thead>
      <tbody>
        <{foreach item=m from=$xm_modules}>
        <tr>
          <td><{$m.module_id}></td>
          <td><code><{$m.dirname|escape}></code></td>
          <td><{$m.name|escape}></td>
          <td><{$m.rule_count}></td>
          <td>
            <{if $m.is_active}>
            <span class="xm-badge xm-badge--active">Active</span>
            <{else}>
            <span class="xm-badge xm-badge--inactive">Off</span>
            <{/if}>
          </td>
          <td class="xm-actions">
            <a href="modules.php?op=edit&amp;module_id=<{$m.module_id}>" class="xm-btn xm-btn--xs xm-btn--default"><{$smarty.const._AM_SUBSCRIPTIONS_EDIT}></a>
            <a href="modules.php?op=delete&amp;module_id=<{$m.module_id}>&amp;token=<{$m.del_token|escape:'url'}>"
               class="xm-btn xm-btn--xs xm-btn--danger"
               onclick="return confirm('<{$smarty.const._AM_SUBSCRIPTIONS_CONFIRM_DELETE|escape:javascript}>')">
              <{$smarty.const._AM_SUBSCRIPTIONS_DELETE}>
            </a>
          </td>
        </tr>
        <{/foreach}>
      </tbody>
    </table>
  </div>
  <{else}>
  <div class="xm-empty-state">
    <p>No modules connected yet.</p>
    <a href="modules.php?op=edit" class="xm-btn xm-btn--primary"><{$smarty.const._AM_SUBSCRIPTIONS_MODULE_ADD}></a>
  </div>
  <{/if}>
</div>
