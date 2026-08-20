<style>
    .helpdesk-shell { padding-top: 12px; }
    .helpdesk-hero {
        display: flex; justify-content: space-between; gap: 16px; align-items: center;
        padding: 24px; margin-bottom: 20px; border-radius: 18px;
        background: linear-gradient(135deg, #0b5cab, #2f87d5 55%, #7fc4ff);
        color: #fff;
    }
    .helpdesk-kicker {
        display: inline-block; margin-bottom: 10px; padding: 5px 10px; border-radius: 999px;
        background: rgba(255,255,255,.18); font-size: 12px; letter-spacing: .08em; text-transform: uppercase;
    }
    .helpdesk-hero h2 { margin: 0 0 6px; font-weight: 700; }
    .helpdesk-hero p { margin: 0; opacity: .92; }
    .helpdesk-actions { display: flex; gap: 10px; flex-wrap: wrap; }
    .helpdesk-stat-card, .helpdesk-card {
        border: 0; border-radius: 16px; box-shadow: 0 10px 24px rgba(15, 57, 105, .08);
    }
    .helpdesk-stat-card {
        padding: 18px 20px; background: #fff; border-left: 5px solid #0b5cab;
    }
    .helpdesk-stat-value { font-size: 30px; font-weight: 700; color: #0f2f52; }
    .helpdesk-stat-label { color: #58728d; font-size: 14px; }
    .helpdesk-card .card-header {
        background: #fff; border-bottom: 1px solid #e8eef5; border-radius: 16px 16px 0 0;
    }
    .helpdesk-card .card-body { background: #fff; border-radius: 0 0 16px 16px; }
    .helpdesk-empty {
        padding: 36px 20px; border: 1px dashed #c7d7e8; border-radius: 16px; background: #fff; text-align: center;
    }
    @media (max-width: 768px) {
        .helpdesk-hero { flex-direction: column; align-items: flex-start; }
    }
</style>
