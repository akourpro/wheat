<?php
/**
 * واجهة تذكير بالدعاء للميت
 * يمكن تضمين هذا الملف مباشرة أو نسخه داخل نظام القمح.
 */
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تذكير بالدعاء للميت</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            color-scheme: only light;
            --primary: #5d89f0;
            --primary-soft: rgba(93, 137, 240, 0.15);
            --accent: #f0c75d;
            --text-main: #1b1d21;
            --text-muted: #5f6368;
            --surface: #ffffff;
            --surface-soft: rgba(255, 255, 255, 0.7);
            --border: #e3e7ef;
            --shadow: 0 24px 48px rgba(15, 35, 95, 0.12), 0 4px 16px rgba(15, 35, 95, 0.08);
            --radius-lg: 28px;
            --radius-md: 16px;
            --radius-sm: 12px;
            --transition: 180ms ease;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Tajawal', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: radial-gradient(circle at top, rgba(93, 137, 240, 0.15), transparent 60%),
                        radial-gradient(circle at bottom, rgba(240, 199, 93, 0.12), transparent 55%),
                        #f5f7ff;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 16px;
            color: var(--text-main);
        }

        main {
            width: min(960px, 100%);
            display: grid;
            gap: 32px;
        }

        .hero-card {
            display: grid;
            gap: 18px;
            background: var(--surface);
            border-radius: var(--radius-lg);
            padding: 36px clamp(24px, 5vw, 48px);
            box-shadow: var(--shadow);
            position: relative;
            overflow: hidden;
        }

        .hero-card::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(93, 137, 240, 0.15), rgba(240, 199, 93, 0.15));
            opacity: 0.85;
            pointer-events: none;
        }

        .hero-card > * {
            position: relative;
            z-index: 2;
        }

        .hero-card h1 {
            font-size: clamp(26px, 3.4vw, 36px);
            margin: 0;
            color: var(--text-main);
            font-weight: 700;
        }

        .hero-card p {
            margin: 0;
            font-size: 17px;
            line-height: 1.8;
            color: var(--text-muted);
            max-width: 62ch;
        }

        .hero-card blockquote {
            margin: 12px 0 0;
            padding: 18px 20px;
            border-radius: var(--radius-md);
            background: var(--surface-soft);
            border: 1px solid rgba(255, 255, 255, 0.6);
            font-size: 18px;
            font-weight: 500;
            color: var(--text-main);
        }

        .form-card {
            background: var(--surface);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow);
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            overflow: hidden;
        }

        .form-card__form {
            padding: clamp(24px, 5vw, 40px);
            display: grid;
            gap: 24px;
            background: rgba(255, 255, 255, 0.96);
        }

        .form-card__preview {
            background: linear-gradient(160deg, rgba(93, 137, 240, 0.38), rgba(27, 29, 33, 0.82));
            color: #fff;
            padding: clamp(28px, 6vw, 48px);
            display: grid;
            gap: 24px;
            align-content: space-between;
        }

        .form-card__preview h2 {
            margin: 0;
            font-size: clamp(22px, 2.6vw, 28px);
            font-weight: 700;
        }

        .form-card__preview p {
            margin: 0;
            font-size: 16px;
            line-height: 1.7;
            color: rgba(255, 255, 255, 0.86);
        }

        .reminder-sample {
            background: rgba(255, 255, 255, 0.1);
            border-radius: var(--radius-md);
            padding: 20px;
            display: grid;
            gap: 12px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .reminder-sample span {
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: rgba(255, 255, 255, 0.75);
        }

        .reminder-sample strong {
            font-size: 18px;
            line-height: 1.6;
            display: block;
        }

        .form-group {
            display: grid;
            gap: 10px;
        }

        .form-group label {
            font-weight: 600;
            color: var(--text-main);
            font-size: 15px;
        }

        .form-group input[type="text"],
        .form-group input[type="tel"] {
            border-radius: var(--radius-sm);
            border: 1px solid var(--border);
            padding: 14px 16px;
            font-size: 16px;
            transition: border-color var(--transition), box-shadow var(--transition);
        }

        .form-group input:focus {
            outline: none;
            border-color: rgba(93, 137, 240, 0.7);
            box-shadow: 0 0 0 4px var(--primary-soft);
        }

        .method-options {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .method-card {
            flex: 1;
            min-width: 160px;
            position: relative;
        }

        .method-card input {
            position: absolute;
            inset: 0;
            opacity: 0;
            cursor: pointer;
        }

        .method-card label {
            display: grid;
            gap: 10px;
            padding: 16px;
            border-radius: var(--radius-md);
            border: 1.5px solid var(--border);
            background: #fff;
            text-align: center;
            transition: border-color var(--transition), transform var(--transition), box-shadow var(--transition);
            font-weight: 500;
            color: var(--text-main);
        }

        .method-card label span {
            font-size: 13px;
            color: var(--text-muted);
        }

        .method-card input:checked + label {
            border-color: var(--primary);
            box-shadow: 0 12px 18px rgba(93, 137, 240, 0.22);
            transform: translateY(-4px);
        }

        .helper-text {
            font-size: 13.5px;
            color: var(--text-muted);
            line-height: 1.6;
        }

        .submit-btn {
            border: none;
            border-radius: 999px;
            background: linear-gradient(135deg, var(--primary), #3c5cc8);
            color: white;
            font-size: 17px;
            font-weight: 600;
            padding: 16px;
            cursor: pointer;
            box-shadow: 0 16px 26px rgba(93, 137, 240, 0.32);
            transition: transform var(--transition), box-shadow var(--transition), filter var(--transition);
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 30px rgba(93, 137, 240, 0.42);
            filter: brightness(1.05);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .footer-note {
            text-align: center;
            font-size: 13.5px;
            color: var(--text-muted);
        }

        @media (max-width: 900px) {
            .form-card {
                grid-template-columns: 1fr;
            }

            .form-card__preview {
                border-top: 1px solid rgba(255, 255, 255, 0.15);
            }
        }

        @media (max-width: 600px) {
            .hero-card {
                padding: 28px 24px;
            }

            .method-options {
                flex-direction: column;
            }

            .method-card {
                min-width: 100%;
            }
        }
    </style>
</head>
<body>
    <main>
        <section class="hero-card">
            <h1>ذكّر نفسك بالدعاء لمن تحب</h1>
            <p>
                ساعد قلبك على الاستمرار في الدعاء للراحلين عن الدنيا. أنشئ تذكيرًا باسم من تحب،
                وحدد الطريقة التي تفضّل أن يصلك بها التنبيه، لتبقى صلتك الروحية ممتدة بالدعاء.
            </p>
            <blockquote>
                «الدعاء يصل الميت في قبره فيفرح به أكثر من فرح أهل الدنيا بالهدايا»
            </blockquote>
        </section>

        <section class="form-card">
            <form class="form-card__form" autocomplete="off">
                <div class="form-group">
                    <label for="deceased-name">اسم المتوفى</label>
                    <input type="text" id="deceased-name" name="deceased_name" placeholder="مثال: جدتي فاطمة" required>
                    <p class="helper-text">اكتب الاسم كما ترغب أن يظهر لك في التذكير.</p>
                </div>

                <div class="form-group">
                    <label>طريقة التذكير</label>
                    <div class="method-options">
                        <div class="method-card">
                            <input type="radio" id="whatsapp-option" name="reminder_method" value="whatsapp" checked>
                            <label for="whatsapp-option">
                                واتساب
                                <span>وصول التذكير إلى رقم هاتفك على واتساب</span>
                            </label>
                        </div>
                        <div class="method-card">
                            <input type="radio" id="telegram-option" name="reminder_method" value="telegram">
                            <label for="telegram-option">
                                تيليجرام
                                <span>استلام تنبيه عبر رسالة خاصة أو بوت تيليجرام</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label id="contact-label" for="contact-value">رقم الجوال (مع مفتاح الدولة)</label>
                    <input type="tel" id="contact-value" name="contact_value" placeholder="+9665xxxxxxx" inputmode="tel" required>
                    <p class="helper-text" id="contact-helper">
                        تأكد من كتابة الرقم مع مفتاح الدولة ليتعرف النظام على وجهة الإرسال.
                    </p>
                </div>

                <button type="submit" class="submit-btn">إنشاء تذكير بالدعاء</button>

                <p class="footer-note">
                    يمكنك تعديل طريقة التذكير متى ما شئت، وسيصل إليك إشعار لطيف يدعوك لقراءة الفاتحة أو الدعاء المختار.
                </p>
            </form>

            <aside class="form-card__preview" aria-hidden="true">
                <div>
                    <h2>نموذج التذكير</h2>
                    <p>
                        ستتلقى رسالة شبيهة بالنص التالي في الموعد الذي تحدده لاحقًا. يمكنك تخصيص الدعاء
                        والنص ليكون أقرب لقلبك وأكثر ما يعبّر عن محبتك.
                    </p>
                </div>

                <div class="reminder-sample">
                    <span>رسالة تذكير</span>
                    <strong id="sample-message">
                        اللهم اغفر لـ <span id="sample-name">جدتي فاطمة</span> وارفع درجتها في عليّين.
                        لا تنس قراءة الفاتحة والدعاء لها اليوم.
                    </strong>
                    <span id="sample-channel">سيصلك هذا التذكير عبر واتساب.</span>
                </div>
            </aside>
        </section>
    </main>

    <script>
        const methodRadios = document.querySelectorAll('input[name="reminder_method"]');
        const contactLabel = document.getElementById('contact-label');
        const contactInput = document.getElementById('contact-value');
        const contactHelper = document.getElementById('contact-helper');
        const deceasedNameInput = document.getElementById('deceased-name');
        const sampleName = document.getElementById('sample-name');
        const sampleChannel = document.getElementById('sample-channel');

        function formatSampleChannel(method) {
            if (method === 'telegram') {
                return 'سيصلك هذا التذكير عبر تيليجرام.';
            }
            return 'سيصلك هذا التذكير عبر واتساب.';
        }

        function updateContactField() {
            const selectedMethod = document.querySelector('input[name="reminder_method"]:checked').value;

            if (selectedMethod === 'telegram') {
                contactLabel.textContent = 'اسم المستخدم على تيليجرام';
                contactInput.type = 'text';
                contactInput.placeholder = 'مثال: @username';
                contactInput.setAttribute('pattern', '^@?[A-Za-z0-9_]{5,}$');
                contactInput.setAttribute('inputmode', 'text');
                contactHelper.textContent = 'استخدم اسم المستخدم الذي يبدأ بعلامة @ أو اكتبه بدونها.';
            } else {
                contactLabel.textContent = 'رقم الجوال (مع مفتاح الدولة)';
                contactInput.type = 'tel';
                contactInput.placeholder = '+9665xxxxxxx';
                contactInput.removeAttribute('pattern');
                contactInput.setAttribute('inputmode', 'tel');
                contactHelper.textContent = 'تأكد من كتابة الرقم مع مفتاح الدولة ليتعرف النظام على وجهة الإرسال.';
            }

            sampleChannel.textContent = formatSampleChannel(selectedMethod);
        }

        function updateSampleName() {
            const name = deceasedNameInput.value.trim();
            sampleName.textContent = name !== '' ? name : 'جدتي فاطمة';
        }

        methodRadios.forEach((radio) => {
            radio.addEventListener('change', updateContactField);
        });

        deceasedNameInput.addEventListener('input', updateSampleName);

        document.addEventListener('DOMContentLoaded', () => {
            updateContactField();
            updateSampleName();
        });
    </script>
</body>
</html>
