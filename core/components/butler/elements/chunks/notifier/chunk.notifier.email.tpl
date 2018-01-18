<p>Dear [[+user.profile_fullname]],</p>
<p>Butler ran task '[[+task.name]]' at '[[+run.start]]'. The following messages were logged:</p>

<table cellpadding="10">
  <tr>
    <th>Source</th>
    <th>Type</th>
    <th>Message</th>
    <th>Stamp</th>
  </tr>
  [[getButlerLog? &run_id=`[[+run.id]]` &rowTpl=`butlerLogRowTpl`]]

</table>

<p>Kind Regards,<br>The [[++site_name]] Team</p>