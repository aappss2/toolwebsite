
<div class="form-group"><label class="form-label">Loan Amount</label><input type="number" id="principal" class="form-input" placeholder="100000"></div>
<div class="form-group"><label class="form-label">Annual Interest Rate (%)</label><input type="number" id="rate" class="form-input" placeholder="8.5" step="0.01"></div>
<div class="form-group"><label class="form-label">Tenure (months)</label><input type="number" id="months" class="form-input" placeholder="60"></div>
<button class="btn btn-primary" onclick="calcEMI()">Calculate EMI</button>
<div id="emiResult" style="margin-top:20px;display:none;" class="card"></div>
<script>
function calcEMI(){
  const P = parseFloat(document.getElementById('principal').value);
  const annual = parseFloat(document.getElementById('rate').value);
  const N = parseInt(document.getElementById('months').value);
  if(!P||!annual||!N){ showToast('Fill all','error'); return; }
  const r = annual/12/100;
  const emi = P * r * Math.pow(1+r,N) / (Math.pow(1+r,N)-1);
  const total = emi*N;
  const interest = total-P;
  document.getElementById('emiResult').style.display='block';
  document.getElementById('emiResult').innerHTML = '<h3>EMI: ₹'+emi.toFixed(2)+'</h3><p>Total: ₹'+total.toFixed(2)+'</p><p>Interest: ₹'+interest.toFixed(2)+'</p>';
  executeTool('car-loan-emi-calculator', {input: P+'-'+annual+'-'+N}, ()=>{});
}
</script>
