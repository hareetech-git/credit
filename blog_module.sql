-- Blog module table
CREATE TABLE IF NOT EXISTS `blogs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `short_description` text DEFAULT NULL,
  `content` longtext NOT NULL,
  `featured_image` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=published,0=draft',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_blog_slug` (`slug`),
  KEY `idx_blog_status` (`status`),
  KEY `idx_blog_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO blogs (title, slug, short_description, content, featured_image, status, created_by) VALUES
('Personal Loan Complete Guide 2026', 'personal-loan-complete-guide-2026', 'Everything you need to know before applying for a personal loan in India.', '<h2>Introduction</h2><p>Personal loans are one of the fastest ways to manage planned and unplanned expenses. They are unsecured, flexible, and available with quick disbursal from banks and NBFCs.</p><h2>Eligibility Basics</h2><p>Most lenders check age, monthly income, employment type, city category, repayment history, and credit profile. Keeping these strong improves your approval probability.</p><h2>Documents Required</h2><ul><li>Identity proof</li><li>Address proof</li><li>Income proof</li><li>Bank statements</li></ul><h2>Final Tip</h2><p>Always compare multiple offers and choose the one with the lowest total repayment, not just the lowest EMI.</p>', 'uploads/blog/blog_1771231198_853.jpeg', 1, 1);

INSERT INTO blogs (title, slug, short_description, content, featured_image, status, created_by) VALUES
('How to Improve CIBIL Score Fast', 'how-to-improve-cibil-score-fast', 'Simple actions to increase your credit score before applying for a loan.', '<h2>Why Score Matters</h2><p>Your credit score strongly impacts approval, interest rate, and loan amount. A better score often means better terms.</p><h2>Practical Steps</h2><ol><li>Pay all EMIs and credit cards on time.</li><li>Keep utilization below 30%.</li><li>Avoid multiple loan applications together.</li><li>Check report errors and raise disputes.</li></ol><h2>Expected Timeline</h2><p>With consistent discipline, score improvement may be visible within a few months.</p>', 'uploads/blog/blog_1771231198_853.jpeg', 1, 1);

INSERT INTO blogs (title, slug, short_description, content, featured_image, status, created_by) VALUES
('Home Loan EMI Planning Strategy', 'home-loan-emi-planning-strategy', 'Plan your EMI smartly to keep long-term finances healthy.', '<h2>Start with Budgeting</h2><p>Calculate fixed monthly obligations first. Keep housing EMI at a manageable percentage of net monthly income.</p><h2>Choose Tenure Wisely</h2><p>Long tenure reduces EMI but increases total interest. Short tenure saves interest but increases monthly burden.</p><h2>Use Part-Prepayment</h2><p>Even occasional part-prepayment can significantly reduce overall loan cost.</p>', 'uploads/blog/blog_1771231198_853.jpeg', 1, 1);

INSERT INTO blogs (title, slug, short_description, content, featured_image, status, created_by) VALUES
('Business Loan for Small Enterprises', 'business-loan-for-small-enterprises', 'A practical guide for MSMEs and small businesses to secure funding.', '<h2>Funding Needs</h2><p>Business loans can support inventory, expansion, working capital, machinery purchase, and seasonal cash-flow gaps.</p><h2>What Lenders Check</h2><ul><li>Business vintage</li><li>Turnover trend</li><li>Banking behavior</li><li>GST/IT returns</li></ul><h2>Improve Approval Chances</h2><p>Maintain clear books, stable transactions, and complete documentation.</p>', 'uploads/blog/blog_1771231198_853.jpeg', 1, 1);

INSERT INTO blogs (title, slug, short_description, content, featured_image, status, created_by) VALUES
('Top Reasons Loan Applications Get Rejected', 'top-reasons-loan-applications-get-rejected', 'Understand common rejection factors and how to avoid them.', '<h2>Frequent Rejection Causes</h2><ol><li>Low or unstable income</li><li>Poor credit score</li><li>Existing high obligations</li><li>Incomplete documentation</li><li>Frequent job changes</li></ol><h2>How to Prevent Rejection</h2><p>Apply with accurate details, improve score first, and pick products matching your profile.</p>', 'uploads/blog/blog_1771231198_853.jpeg', 1, 1);

INSERT INTO blogs (title, slug, short_description, content, featured_image, status, created_by) VALUES
('Gold Loan Explained for Beginners', 'gold-loan-explained-for-beginners', 'Fast liquidity through gold loans: process, benefits, and costs.', '<h2>Why Gold Loan</h2><p>Gold loans are secured, quick, and usually require minimal paperwork compared to other loan types.</p><h2>Key Terms</h2><ul><li>Loan-to-Value (LTV)</li><li>Interest rate type</li><li>Auction policy on default</li><li>Processing and valuation charges</li></ul><h2>Important Note</h2><p>Check repayment schedule and hidden charges before finalizing.</p>', 'uploads/blog/blog_1771231198_853.jpeg', 1, 1);

INSERT INTO blogs (title, slug, short_description, content, featured_image, status, created_by) VALUES
('Balance Transfer: When It Saves Money', 'balance-transfer-when-it-saves-money', 'Know the right time to shift your existing loan to a lower rate.', '<h2>What is Balance Transfer</h2><p>It allows you to move your outstanding loan to another lender offering better terms.</p><h2>When to Consider</h2><p>Consider transfer when rate gap is meaningful and remaining tenure is long enough to recover switching costs.</p><h2>Checklist</h2><ol><li>Processing fee</li><li>Legal/technical charges</li><li>New EMI impact</li><li>Net savings</li></ol>', 'uploads/blog/blog_1771231198_853.jpeg', 1, 1);

INSERT INTO blogs (title, slug, short_description, content, featured_image, status, created_by) VALUES
('Car Loan Offer Comparison Framework', 'car-loan-offer-comparison-framework', 'A clear method to compare car loan options from different lenders.', '<h2>Compare Total Cost</h2><p>Do not compare only interest rates. Include processing fees, insurance bundling, foreclosure terms, and penalty clauses.</p><h2>Down Payment Impact</h2><p>Higher down payment reduces principal and can improve rate eligibility.</p><h2>Final Selection</h2><p>Choose the lender offering transparent terms and lower effective cost over tenure.</p>', 'uploads/blog/blog_1771231198_853.jpeg', 1, 1);

INSERT INTO blogs (title, slug, short_description, content, featured_image, status, created_by) VALUES
('EMI vs Tenure: Smart Decision Guide', 'emi-vs-tenure-smart-decision-guide', 'How to choose between low EMI comfort and low-interest savings.', '<h2>Two Opposite Priorities</h2><p>Long tenure supports cash flow with lower EMI. Short tenure minimizes total interest outgo.</p><h2>Decision Formula</h2><p>If monthly cash flow is tight, prioritize EMI comfort. If income is stable, reduce tenure to save total interest.</p><h2>Pro Tip</h2><p>Start with safer EMI and prepay whenever possible.</p>', 'uploads/blog/blog_1771231198_853.jpeg', 1, 1);

INSERT INTO blogs (title, slug, short_description, content, featured_image, status, created_by) VALUES
('MSME Loan Document Readiness Checklist', 'msme-loan-document-readiness-checklist', 'Prepare the right documents to get MSME loan approvals faster.', '<h2>Core Documents</h2><ul><li>Business registration proof</li><li>PAN and identity/address proof</li><li>Bank statements</li><li>Income tax returns</li><li>GST returns (if applicable)</li></ul><h2>Why Readiness Matters</h2><p>Complete documentation reduces verification delays and increases lender confidence.</p><h2>Before You Apply</h2><p>Ensure all records are updated and consistent across documents.</p>', 'uploads/blog/blog_1771231198_853.jpeg', 1, 1);

