<?php
// This file is included from index.php, so the $conn variable is available.
$guide_result = $conn->query("SELECT * FROM visitor_guide ORDER BY sort_order ASC");
$guide_items = [];
if ($guide_result && $guide_result->num_rows > 0) {
    while($row = $guide_result->fetch_assoc()) {
        $guide_items[] = $row;
    }
}
?>
<style>
    .visitor-guide-section {
        padding: 5rem 2rem;
        background: var(--light-bg);
        background-image: url('https://www.toptal.com/designers/subtlepatterns/uploads/om.png');
    }
    .visitor-guide-container {
        max-width: 1100px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: 1fr 1.5fr;
        gap: 4rem;
        align-items: center;
    }
    .guide-intro-col h2 {
        font-size: 2.8rem;
        color: var(--primary-red);
        line-height: 1.2;
        margin-bottom: 1.5rem;
    }
    .guide-intro-col p {
        font-size: 1.1rem;
        color: #555;
        line-height: 1.8;
    }
    .guide-intro-col img {
        width: 100%;
        border-radius: 15px;
        margin-top: 2rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    .accordion-col {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }
    .accordion-item {
        background: var(--white);
        border-radius: 12px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        overflow: hidden;
        opacity: 0;
        transform: translateY(20px);
        transition: opacity 0.6s ease, transform 0.6s ease;
    }
    .accordion-item.is-visible {
        opacity: 1;
        transform: translateY(0);
    }
    .accordion-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1.5rem;
        cursor: pointer;
    }
    .accordion-header .fas {
        font-size: 1.5rem;
        color: var(--accent-orange);
    }
    .accordion-header h4 {
        font-size: 1.3rem;
        flex-grow: 1;
        color: var(--dark-text);
    }
    .accordion-toggle {
        font-size: 1.2rem;
        transition: transform 0.3s ease;
        color: var(--primary-red);
    }
    .accordion-item.active .accordion-toggle {
        transform: rotate(45deg);
    }
    .accordion-content {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.5s ease-in-out, padding 0.5s ease-in-out;
    }
    .accordion-content p {
        padding: 0 1.5rem 1.5rem 1.5rem;
        color: #555;
        line-height: 1.7;
    }
    @media (max-width: 900px) {
        .visitor-guide-container { grid-template-columns: 1fr; }
        .guide-intro-col { text-align: center; }
    }
</style>

<section class="visitor-guide-section">
    <div class="visitor-guide-container">
        <div class="guide-intro-col">
            <h2>Visitor Guide</h2>
            <p>We welcome you with open hearts. To make your visit serene and fulfilling, here is some helpful information about our temple's activities and timings.</p>
            
            <img src="https://picsum.photos/id/1043/800/600" alt="Temple Entrance">
        </div>
        <div class="accordion-col">
            <?php if (!empty($guide_items)): 
                foreach($guide_items as $index => $item): ?>
            <div class="accordion-item animate-on-scroll-guide" style="transition-delay: <?php echo $index * 0.1; ?>s">
                <div class="accordion-header">
                    <i class="<?php echo htmlspecialchars($item['icon_class']); ?>"></i>
                    <h4><?php echo htmlspecialchars($item['title']); ?></h4>
                    <i class="fas fa-plus accordion-toggle"></i>
                </div>
                <div class="accordion-content">
                    <p><?php echo nl2br(htmlspecialchars($item['content'])); ?></p>
                </div>
            </div>
            <?php endforeach; else: ?>
                <p>Visitor information will be updated soon.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const accordionItems = document.querySelectorAll('.accordion-item');
    accordionItems.forEach(item => {
        const header = item.querySelector('.accordion-header');
        const content = item.querySelector('.accordion-content');
        header.addEventListener('click', () => {
            const isActive = item.classList.contains('active');
            accordionItems.forEach(i => {
                i.classList.remove('active');
                i.querySelector('.accordion-content').style.maxHeight = null;
                i.querySelector('.accordion-toggle').style.transform = 'rotate(0deg)';
            });
            if (!isActive) {
                item.classList.add('active');
                content.style.maxHeight = content.scrollHeight + "px";
                item.querySelector('.accordion-toggle').style.transform = 'rotate(45deg)';
            }
        });
    });
    
    const animatedItems = document.querySelectorAll('.animate-on-scroll-guide');
    if ("IntersectionObserver" in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });

        animatedItems.forEach(item => observer.observe(item));
    }
});
</script>

