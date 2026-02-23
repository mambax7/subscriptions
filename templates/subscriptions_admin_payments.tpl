<div class="subscriptions-admin">

  <h1 class="xm-admin-title"><{$smarty.const._AM_SUBSCRIPTIONS_PAYMENT_LIST}> <span class="xm-count">(<{$xm_total}>)</span></h1>

  <{if $xm_payments}>
  <div class="xm-table-wrap">
    <table class="xm-table xm-sortable" role="table">
      <thead>
        <tr>
          <th scope="col">ID</th>
          <th scope="col">User</th>
          <th scope="col">Gateway</th>
          <th scope="col">Amount</th>
          <th scope="col">Status</th>
          <th scope="col">Paid At</th>
          <th scope="col" class="sorter-false"><{$smarty.const._AM_SUBSCRIPTIONS_ACTIONS}></th>
        </tr>
      </thead>
      <tbody>
        <{foreach item=p from=$xm_payments}>
        <tr>
          <td><{$p.payment_id}></td>
          <td><{$p.username|escape}></td>
          <td><{$p.gateway|escape}></td>
          <td><{$p.amount}></td>
          <td><span class="xm-badge xm-badge--<{$p.status}>"><{$p.status|capitalize}></span></td>
          <td><{$p.paid_at}></td>
          <td class="xm-actions">
            <a href="subscriptions.php?user_id=<{$p.user_id}>" class="xm-btn xm-btn--xs xm-btn--default"
               title="<{$smarty.const._AM_SUBSCRIPTIONS_USER_HISTORY}>"><{$smarty.const._AM_SUBSCRIPTIONS_USER_HISTORY}></a>
            <{if $p.status eq 'completed'}>
            <button type="button" class="xm-btn xm-btn--xs xm-btn--secondary"
                    onclick="document.getElementById('refund-form-<{$p.payment_id}>').style.display='block'">
              <{$smarty.const._AM_SUBSCRIPTIONS_ISSUE_REFUND}>
            </button>
            <div id="refund-form-<{$p.payment_id}>" style="display:none;margin-top:8px">
              <form method="post" action="payments.php?op=refund">
                <input type="hidden" name="token"      value="<{$p.refund_token}>">
                <input type="hidden" name="payment_id" value="<{$p.payment_id}>">
                <input type="number" name="amount"     step="0.01" min="0.01"
                       placeholder="<{$smarty.const._AM_SUBSCRIPTIONS_REFUND_AMOUNT}>" class="xm-input xm-input--sm">
                <input type="text"   name="reason"
                       placeholder="<{$smarty.const._AM_SUBSCRIPTIONS_REFUND_REASON}>" class="xm-input xm-input--sm">
                <button type="submit" class="xm-btn xm-btn--xs xm-btn--danger"><{$smarty.const._AM_SUBSCRIPTIONS_ISSUE_REFUND}></button>
              </form>
            </div>
            <{/if}>
          </td>
        </tr>
        <{/foreach}>
      </tbody>
    </table>
  </div>
  <div class="xm-pagenav"><{$xm_pagenav}></div>
  <{else}>
  <p class="xm-empty">No payments found.</p>
  <{/if}>
</div>
