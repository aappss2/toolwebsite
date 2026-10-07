
<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
<div class="form-group"><label class="form-label">Weight (kg)</label><input type="number" id="weight" class="form-input" placeholder="70"></div>
<div class="form-group"><label class="form-label">Height (cm)</label><input type="number" id="height" class="form-input" placeholder="175"></div>
</div>
<button class="btn btn-primary" onclick="calcBMI()">Calculate BMI</button>
<div id="bmiResult" style="margin-top:20px;display:none;" class="card"></div>
<script>
function calcBMI(){
  const w = parseFloat(document.getElementById('weight').value);
  const h = parseFloat(document.getElementById('height').value)/100;
  if(!w||!h){ showToast('Enter weight and height','error'); return; }
  const bmi = w/(h*h);
  let cat = bmi<18.5?'Underweight':bmi<25?'Normal':bmi<30?'Overweight':'Obese';
  document.getElementById('bmiResult').style.display='block';
  document.getElementById('bmiResult').innerHTML = '<h3>BMI: '+bmi.toFixed(2)+' — '+cat+'</h3>';
  executeTool('bmi-calculator', {input: w+'-'+h}, ()=>{});
}
</script>
