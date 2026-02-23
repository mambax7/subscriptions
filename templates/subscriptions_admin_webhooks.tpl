<div class="subscriptions-admin">

  <h1 class="xm-admin-title"><{$smarty.const._AM_SUBSCRIPTIONS_WEBHOOK_LIST}></h1>
  <div class="xm-toolbar">
    <a href="webhooks.php?op=edit" class="xm-btn xm-btn--sm xm-btn--primary"><{$smarty.const._AM_SUBSCRIPTIONS_WEBHOOK_ADD}></a>
  </div>

  <{if $xm_webhooks}>
  <div class="xm-table-wrap">
    <table class="xm-table xm-sortable" role="table">
      <thead>
        <tr>
          <th scope="col">ID</th>
          <th scope="col"><{$smarty.const._AM_SUBSCRIPTIONS_WEBHOOK_URL}></th>
          <th scope="col"><{$smarty.const._AM_SUBSCRIPTIONS_WEBHOOK_EVENTS}></th>
          <th scope="col">Failures</th>
          <th scope="col">Active</th>
          <th scope="col" class="sorter-false"><{$smarty.const._AM_SUBSCRIPTIONS_ACTIONS}></th>
        </tr>
      </thead>
      <tbody>
        <{foreach item=w from=$xm_webhooks}>
        <tr>
          <td><{$w.webhook_id}></td>
          <td><code><{$w.url|escape}></code></td>
          <td><{$w.events|escape}></td>
          <td><{$w.fail_count}></td>
          <td>
            <{if $w.is_active}>
            <span class="xm-badge xm-badge--active">Active</span>
            <{else}>
            <span class="xm-badge xm-badge--inactive">Off</span>
            <{/if}>
          </td>
          <td class="xm-actions">
            <a href="webhooks.php?op=edit&amp;webhook_id=<{$w.webhook_id}>" class="xm-btn xm-btn--xs xm-btn--default"><{$smarty.const._AM_SUBSCRIPTIONS_EDIT}></a>
            <a href="webhooks.php?op=delete&amp;webhook_id=<{$w.webhook_id}>&amp;token=<{$w.del_token|escape:'url'}>"
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
  <p class="xm-empty">No webhooks configured. <a href="webhooks.php?op=edit"><{$smarty.const._AM_SUBSCRIPTIONS_WEBHOOK_ADD}></a></p>
  <{/if}>
</div>
