<style>
    /* --- THEME COLORS --- */
    :root {
        --primary-red: #9F0102;
        --accent-orange: #FF6D01;
        --light-bg: #FFEFDA;
        --dark-text: #333333;
        --white: #ffffff;
        --gray: #f9f9f9;
        --success-green: #28a745;
    }

    /* --- DONATION SECTION STYLES --- */
    .donation-section {
        padding: 5rem 2rem;
        background-color: var(--light-bg);
        background-image: url('https://www.toptal.com/designers/subtlepatterns/uploads/om.png');
    }
    .donation-container {
        max-width: 800px;
        margin: 0 auto;
        background: var(--white);
        padding: 3rem;
        border-radius: 20px;
        box-shadow: 0 15px 40px rgba(0,0,0,0.1);
        text-align: center;
    }
    .section-heading {
        font-size: 2.8rem;
        color: var(--primary-red);
        margin-bottom: 1rem;
    }
    .donation-intro {
        color: #555;
        font-size: 1.1rem;
        margin-bottom: 2.5rem;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
    }

    .donation-form {
        text-align: left;
    }
    .form-group {
        margin-bottom: 2rem;
    }
    .form-group label, .form-step-title {
        display: block;
        font-weight: bold;
        margin-bottom: 1rem;
        font-size: 1.3rem;
        color: var(--dark-text);
    }
    .personal-details-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }
    .input-wrapper {
        position: relative;
    }
    .input-icon {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--accent-orange);
    }
    .form-group input {
        width: 100%;
        padding: 0.8rem 1rem 0.8rem 2.8rem;
        border-radius: 8px;
        border: 1px solid #ccc;
        font-size: 1rem;
    }

    .reason-options {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 1rem;
    }
    .reason-option input { display: none; }
    .reason-content {
        background: var(--gray);
        padding: 1.5rem 1rem;
        border-radius: 10px;
        border: 2px solid #ddd;
        cursor: pointer;
        text-align: center;
        transition: all 0.3s ease;
    }
    .reason-content .fas {
        font-size: 2rem;
        color: var(--accent-orange);
        margin-bottom: 0.75rem;
    }
    .reason-option input:checked + .reason-content {
        border-color: var(--accent-orange);
        background: var(--light-bg);
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0,0,0,0.05);
    }

    .amount-options {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        margin-top: 1rem;
    }
    .amount-option input { display: none; }
    .amount-label {
        background: #eee;
        padding: 0.8rem 1.5rem;
        border-radius: 25px;
        cursor: pointer;
        font-weight: bold;
        border: 2px solid transparent;
    }
    .amount-option input:checked + .amount-label {
        background: var(--light-bg);
        border-color: var(--primary-red);
        color: var(--primary-red);
    }
    
    #custom-amount-group { display: none; }
    #custom-amount {
        font-size: 1.5rem;
        padding: 0.8rem;
        border: 2px solid #ddd;
        border-radius: 8px;
        text-align: center;
        width: 100%;
        max-width: 200px;
        margin-top: 1rem;
    }

    .btn-donate-now {
        background: var(--accent-orange);
        color: var(--white);
        border: none;
        padding: 1rem;
        border-radius: 8px;
        font-weight: bold;
        cursor: pointer;
        font-size: 1.2rem;
        width: 100%;
        margin-top: 2rem;
    }
    
    /* --- PAYMENT MODAL --- */
    .payment-modal-overlay {
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.7);
        backdrop-filter: blur(8px);
        z-index: 2000;
        display: flex; align-items: center; justify-content: center;
        opacity: 0; visibility: hidden; transition: opacity 0.3s;
    }
    .payment-modal-overlay.active { opacity: 1; visibility: visible; }
    .payment-modal {
        background: var(--white);
        padding: 2.5rem;
        border-radius: 15px;
        width: 90%;
        max-width: 400px;
        text-align: center;
    }
    #payment-success { display: none; }
    .spinner {
        border: 4px solid #f3f3f3;
        border-top: 4px solid var(--accent-orange);
        border-radius: 50%;
        width: 50px;
        height: 50px;
        animation: spin 1s linear infinite;
        margin: 0 auto 1.5rem auto;
    }
    @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    .success-icon { font-size: 4rem; color: var(--success-green); margin-bottom: 1rem; }
    #payment-amount { font-weight: bold; color: var(--primary-red); }

    @media (max-width: 600px) {
        .donation-container { padding: 2rem 1.5rem; }
        .personal-details-grid { grid-template-columns: 1fr; }
    }
</style>

<section class="donation-section">
    <div class="donation-container">
        <h2 class="section-heading">Support Our Temple</h2>
        <p class="donation-intro">Your generous contribution helps us maintain the temple, perform sacred rituals, and serve the community. Every donation makes a difference.</p>

        <form class="donation-form" id="donation-form">
            <div class="form-group">
                <label class="form-step-title">1. Your Details</label>
                <div class="personal-details-grid">
                    <div class="input-wrapper"><i class="fas fa-user input-icon"></i><input type="text" id="donor-name" placeholder="Full Name" required></div>
                    <div class="input-wrapper"><i class="fas fa-envelope input-icon"></i><input type="email" id="donor-email" placeholder="Email Address" required></div>
                    <div class="input-wrapper full-width"><i class="fas fa-phone-alt input-icon"></i><input type="tel" id="donor-mobile" placeholder="Mobile Number" required></div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-step-title">2. Reason for Donation</label>
                <div class="reason-options">
                    <label class="reason-option"><input type="radio" name="donation_reason" value="Annadaan" checked data-amounts="501,1100,2100"><div class="reason-content"><i class="fas fa-utensils"></i><span>Annadaan</span></div></label>
                    <label class="reason-option"><input type="radio" name="donation_reason" value="Gau Seva" data-amounts="251,501,1100"><div class="reason-content"><i class="fas fa-paw"></i><span>Gau Seva</span></div></label>
                    <label class="reason-option"><input type="radio" name="donation_reason" value="Temple Development" data-amounts="1100,2100,5100"><div class="reason-content"><i class="fas fa-gopuram"></i><span>Temple Development</span></div></label>
                </div>
            </div>

            <div class="form-group">
                <label class="form-step-title">3. Choose an Amount (in INR)</label>
                <div class="amount-options" id="amount-options"></div>
            </div>

             <div class="form-group" id="custom-amount-group">
                <input type="number" id="custom-amount" name="custom_amount" min="101" placeholder="Enter Custom Amount">
            </div>

            <button type="submit" class="btn-donate-now">Donate Now</button>
        </form>
    </div>
</section>

<!-- Payment Modal -->
<div class="payment-modal-overlay" id="payment-modal-overlay">
    <div class="payment-modal">
        <div id="payment-processing">
            <h3>Processing Donation</h3>
            <p>Please wait while we securely process your contribution...</p>
            <div class="spinner"></div>
            <p>Donating <strong id="payment-amount">₹ 0</strong> for <strong id="payment-reason">Reason</strong>.</p>
        </div>
        <div id="payment-success">
            <i class="fas fa-check-circle success-icon"></i>
            <h3>Thank You!</h3>
            <p>Your generous donation has been received. A receipt will be sent to your email.</p>
            <button class="btn-donate-now" id="close-modal-btn" style="margin-top: 1rem;">Close</button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const reasonRadios = document.querySelectorAll('input[name="donation_reason"]');
    const amountOptionsContainer = document.getElementById('amount-options');
    const customAmountGroup = document.getElementById('custom-amount-group');
    const donationForm = document.getElementById('donation-form');
    const modalOverlay = document.getElementById('payment-modal-overlay');
    const paymentProcessing = document.getElementById('payment-processing');
    const paymentSuccess = document.getElementById('payment-success');
    
    function updateAmountOptions(selectedReason) {
        amountOptionsContainer.innerHTML = '';
        const amounts = selectedReason.dataset.amounts.split(',');
        
        amounts.forEach((amount, index) => {
            const option = document.createElement('label');
            option.className = 'amount-option';
            option.innerHTML = `<input type="radio" name="donation_amount" value="${amount}" ${index === 0 ? 'checked' : ''}><span class="amount-label">₹ ${amount}</span>`;
            amountOptionsContainer.appendChild(option);
        });
        
        const otherOption = document.createElement('label');
        otherOption.className = 'amount-option';
        otherOption.innerHTML = `<input type="radio" name="donation_amount" value="other"><span class="amount-label">Other</span>`;
        amountOptionsContainer.appendChild(otherOption);

        document.querySelectorAll('input[name="donation_amount"]').forEach(radio => {
            radio.addEventListener('change', () => {
                customAmountGroup.style.display = radio.value === 'other' ? 'block' : 'none';
            });
        });
        customAmountGroup.style.display = 'none';
    }

    reasonRadios.forEach(radio => {
        radio.addEventListener('change', () => updateAmountOptions(radio));
    });

    const initialReason = document.querySelector('input[name="donation_reason"]:checked');
    if (initialReason) updateAmountOptions(initialReason);

    donationForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        let amount = document.querySelector('input[name="donation_amount"]:checked').value;
        const reason = document.querySelector('input[name="donation_reason"]:checked').value;
        if (amount === 'other') {
            amount = document.getElementById('custom-amount').value;
        }

        document.getElementById('payment-amount').textContent = `₹ ${amount}`;
        document.getElementById('payment-reason').textContent = reason;
        
        paymentProcessing.style.display = 'block';
        paymentSuccess.style.display = 'none';
        modalOverlay.classList.add('active');

        // Simulate payment processing
        setTimeout(() => {
            paymentProcessing.style.display = 'none';
            paymentSuccess.style.display = 'block';
        }, 2500); // 2.5 seconds delay
    });
    
    document.getElementById('close-modal-btn').addEventListener('click', () => {
        modalOverlay.classList.remove('active');
    });
});
</script>

