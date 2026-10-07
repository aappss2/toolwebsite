
<div class="form-group"><label class="form-label">Date of Birth</label><input type="date" id="dob" class="form-input"></div>
<div class="form-group"><label class="form-label">As Of (optional)</label><input type="date" id="asof" class="form-input"></div>
<button class="btn btn-primary" onclick="calcAge()">Calculate Age</button>
<div id="ageResult" style="margin-top:20px;display:none;" class="card"></div>
<script>
function calcAge(){
  const dob = document.getElementById('dob').value;
  if(!dob){ showToast('Enter DOB','error'); return; }
  const birth = new Date(dob);
  const now = document.getElementById('asof').value ? new Date(document.getElementById('asof').value) : new Date();
  let years = now.getFullYear() - birth.getFullYear();
  let months = now.getMonth() - birth.getMonth();
  let days = now.getDate() - birth.getDate();
  if(days<0){ months--; days += new Date(now.getFullYear(), now.getMonth(),0).getDate(); }
  if(months<0){ years--; months+=12; }
  const totalDays = Math.floor((now-birth)/(1000*60*60*24));
  document.getElementById('ageResult').style.display='block';
  document.getElementById('ageResult').innerHTML = '<h3>Age: '+years+' years, '+months+' months, '+days+' days</h3><p>Total days: '+totalDays+'</p>';
  executeTool('slugging-percentage-calculator', {input: dob}, (data)=>{});
}
</script>
