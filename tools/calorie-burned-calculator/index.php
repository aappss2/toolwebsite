
<div class="form-group"><label class="form-label">Input Value 1</label><input type="number" id="inp1" class="form-input" placeholder="Enter value"></div>
<div class="form-group"><label class="form-label">Input Value 2</label><input type="number" id="inp2" class="form-input" placeholder="Optional"></div>
<button class="btn btn-primary" onclick="calculateGeneric()">Calculate Calorie Burned Calculator</button>
<div id="genericResult" style="margin-top:20px;display:none;" class="card"></div>
<script>
function calculateGeneric(){
  const v1 = parseFloat(document.getElementById('inp1').value);
  const v2 = parseFloat(document.getElementById('inp2').value);
  if(isNaN(v1)){ showToast('Enter value','error'); return; }
  let result = v1;
  if(!isNaN(v2)) result = v1 + v2;
  document.getElementById('genericResult').style.display='block';
  document.getElementById('genericResult').innerHTML = '<h3>Result: '+result.toFixed(2)+'</h3><p>Tool: Calorie Burned Calculator</p>';
  executeTool('calorie-burned-calculator', {input: v1+'-'+v2}, ()=>{});
}
</script>
