# Migration Log

Date Started: 2026-10-07
Old Domain: https://jdiro.com/
New Domain: https://tool.prompttai.com/

## Overview

This log tracks migration status for each legacy tool from tools.json (181 records) to new unified platform.

Migration Status Values:
- PENDING — not started
- IN_PROGRESS — being migrated
- MIGRATED — completed, tested, working
- NEEDS_MANUAL_REVIEW — unusual, requires human decision
- EXTERNAL — points to ai.jdiro.com, will be proxied or marked external
- DUPLICATE — duplicate title/link, merged

## Batch Strategy

- Batch size: 10-20 tools
- After each batch: test, repair, update log, continue
- No silent omissions

## Tool Migration Table

| # | Title | Old Link | New Slug | New URL | Category (Old -> New) | Status | Notes |
|---|-------|----------|----------|---------|----------------------|--------|-------|

| 1 | 🚀 J1RO-AI (Chat with AI) | https://ai.jdiro.com//JIRO-AI//index.html | j1ro-ai-chat-with-ai | https://tool.prompttai.com/tools/j1ro-ai-chat-with-ai/ | 🤖 AI Tools -> AI Tools | EXTERNAL |  |
| 2 | JIRO-AI GRAMMAR FIXER | https://ai.jdiro.com//ai-grammar-fixer//index.html | jiro-ai-grammar-fixer | https://tool.prompttai.com/tools/jiro-ai-grammar-fixer/ | 🤖 AI Tools -> AI Tools | EXTERNAL |  |
| 3 | JIRO-AI Summarizer | https://ai.jdiro.com//ai-content-summarizer.html | jiro-ai-summarizer | https://tool.prompttai.com/tools/jiro-ai-summarizer/ | 🤖 AI Tools -> AI Tools | EXTERNAL |  |
| 4 | JIRO-AI QUESTION PAPER GENERATOR | https://ai.jdiro.com//ai-question-paper.html | jiro-ai-question-paper-generator | https://tool.prompttai.com/tools/jiro-ai-question-paper-generator/ | 🤖 AI Tools -> AI Tools | EXTERNAL |  |
| 5 | JIRO-AI EMAIL WRITER | https://ai.jdiro.com//ai-email-writer.html | jiro-ai-email-writer | https://tool.prompttai.com/tools/jiro-ai-email-writer/ | 🤖 AI Tools -> AI Tools | EXTERNAL |  |
| 6 | JIRO-AI LETTER WRITER | https://ai.jdiro.com//ai-letter-writer.html | jiro-ai-letter-writer | https://tool.prompttai.com/tools/jiro-ai-letter-writer/ | 🤖 AI Tools -> AI Tools | EXTERNAL |  |
| 7 | JIRO-AI APPLICATION WRITER | https://ai.jdiro.com//ai-application-writer.html | jiro-ai-application-writer | https://tool.prompttai.com/tools/jiro-ai-application-writer/ | 🤖 AI Tools -> AI Tools | EXTERNAL |  |
| 8 | JIRO-AI RESUME BUILDER | https://ai.jdiro.com//ai-resume-builder.html | jiro-ai-resume-builder | https://tool.prompttai.com/tools/jiro-ai-resume-builder/ | 🤖 AI Tools -> AI Tools | EXTERNAL |  |
| 9 | AI Essay & Assignment Writer | https://ai.jdiro.com//AI-Essay-Assignment-Writer//index.html | ai-essay-assignment-writer | https://tool.prompttai.com/tools/ai-essay-assignment-writer/ | 🤖 AI Tools -> AI Tools | EXTERNAL |  |
| 10 | ENGLISH SPEAKING TUTOR | https://ai.jdiro.com//ENGLISH//ai-english-speaking-tutor.html | english-speaking-tutor | https://tool.prompttai.com/tools/english-speaking-tutor/ | 🤖 AI Tools -> AI Tools | EXTERNAL |  |
| 11 | Base64 Encoder / Decoder | base64-encoder-decoder | base64-encoder-decoder | https://tool.prompttai.com/tools/base64-encoder-decoder/ | UTILITY TOOLS -> Utility Tools | MIGRATED |  |
| 12 | Link Status Checker | link-status-checker/index.php | link-status-checker | https://tool.prompttai.com/tools/link-status-checker/ | UTILITY TOOLS -> Utility Tools | MIGRATED |  |
| 13 | Video Converter | video-converter-app/index.html | video-converter | https://tool.prompttai.com/tools/video-converter/ | Audio & Video Tools -> Audio & Video Tools | MIGRATED |  |
| 14 | Z-SCORE CALCULATOR | z-score-calculator/index.html | z-score-calculator | https://tool.prompttai.com/tools/z-score-calculator/ | CALCULATOR -> Calculators | MIGRATED |  |
| 15 | TEXT EDITOR APP | Text Editor App/index.html | text-editor-app | https://tool.prompttai.com/tools/text-editor-app/ | UTILITY TOOLS -> Utility Tools | MIGRATED |  |
| 16 | AGE CALCULATOR | Utility Tools/Age-Calculator.html | age-calculator | https://tool.prompttai.com/tools/age-calculator/ | CALCULATOR -> Calculators | MIGRATED |  |
| 17 | Time Zone Converter | Utility Tools/time-zone-converter.html | time-zone-converter | https://tool.prompttai.com/tools/time-zone-converter/ | UTILITY TOOLS -> Utility Tools | MIGRATED |  |
| 18 | Countdown Timer / Stopwatch | Utility Tools/Countdown-Timer-Stopwatch.html | countdown-timer-stopwatch | https://tool.prompttai.com/tools/countdown-timer-stopwatch/ | UTILITY TOOLS -> Utility Tools | MIGRATED |  |
| 19 | LOAN EMI CALCULATOR | Utility Tools/LOAN-EMI-CALCULATOR.html | loan-emi-calculator | https://tool.prompttai.com/tools/loan-emi-calculator/ | CALCULATOR -> Calculators | MIGRATED |  |
| 20 | Mortgage-Calculator | Utility Tools/Mortgage-Calculator.html | mortgage-calculator | https://tool.prompttai.com/tools/mortgage-calculator/ | CALCULATOR -> Calculators | MIGRATED |  |
| 21 | COMPOUND INTEREST CALCULATOR | Utility Tools/compound-interest-calculator.html | compound-interest-calculator | https://tool.prompttai.com/tools/compound-interest-calculator/ | CALCULATOR -> Calculators | MIGRATED |  |
| 22 | IP Address Finder / Tracker | Utility Tools/IP-Address-Finder-Tracker | ip-address-finder-tracker | https://tool.prompttai.com/tools/ip-address-finder-tracker/ | UTILITY TOOLS -> Utility Tools | MIGRATED |  |
| 23 | CALORIE CALCULATOR | Utility Tools/calorie-calculator.html | calorie-calculator | https://tool.prompttai.com/tools/calorie-calculator/ | CALCULATOR -> Calculators | MIGRATED |  |
| 24 | SIMPLE INTEREST | Utility Tools/simple-interest.html | simple-interest | https://tool.prompttai.com/tools/simple-interest/ | CALCULATOR -> Calculators | MIGRATED |  |
| 25 | SALARY CALCULATOR | Utility Tools/Salary-Calculator.html | salary-calculator | https://tool.prompttai.com/tools/salary-calculator/ | CALCULATOR -> Calculators | MIGRATED |  |
| 26 | INDIA INCOME TAX CALCULATOR | Utility Tools/income-tax-calculator.html | india-income-tax-calculator | https://tool.prompttai.com/tools/india-income-tax-calculator/ | CALCULATOR -> Calculators | MIGRATED |  |
| 27 | SIP CALCULATOR | Utility Tools/Sip-Calculator.html | sip-calculator | https://tool.prompttai.com/tools/sip-calculator/ | CALCULATOR -> Calculators | MIGRATED |  |
| 28 | GST CALCUATOR | Utility Tools/Gst-Calculator.html | gst-calcuator | https://tool.prompttai.com/tools/gst-calcuator/ | CALCULATOR -> Calculators | MIGRATED |  |
| 29 | US INCOME TAX CALCULATOR | Utility Tools/us-income-tax-calculator.html | us-income-tax-calculator | https://tool.prompttai.com/tools/us-income-tax-calculator/ | CALCULATOR -> Calculators | MIGRATED |  |
| 30 | Scientific Calculator | Scientific Calculator/index.html | scientific-calculator | https://tool.prompttai.com/tools/scientific-calculator/ | CALCULATOR -> Calculators | MIGRATED |  |
| 31 | GPA / CGPA Calculator | Educational Tools/GPA&CGPA-Calculator.html | gpa-cgpa-calculator | https://tool.prompttai.com/tools/gpa-cgpa-calculator/ | CALCULATOR -> Calculators | MIGRATED |  |
| 32 | Percentage Calculator | Educational Tools/percentage-calculator.html | percentage-calculator | https://tool.prompttai.com/tools/percentage-calculator/ | CALCULATOR -> Calculators | MIGRATED |  |
| 33 | PERIODIC TABLE | Educational Tools/Periodic-Table.html | periodic-table | https://tool.prompttai.com/tools/periodic-table/ | 📚 Educational Tools -> Educational Tools | MIGRATED |  |
| 34 | MATRIX CALCULATOR | Educational Tools/matrix-calculator.html | matrix-calculator | https://tool.prompttai.com/tools/matrix-calculator/ | CALCULATOR -> Calculators | MIGRATED |  |
| 35 | Graph Plotter | Educational Tools/graph-plotter.html | graph-plotter | https://tool.prompttai.com/tools/graph-plotter/ | 📚 Educational Tools -> Educational Tools | MIGRATED |  |
| 36 | Chemical Equation Balancer | Educational Tools/chemical-equation-balancer.html | chemical-equation-balancer | https://tool.prompttai.com/tools/chemical-equation-balancer/ | 📚 Educational Tools -> Educational Tools | MIGRATED |  |
| 37 | Probability Calculator | Educational Tools/Probability-Calculator.html | probability-calculator | https://tool.prompttai.com/tools/probability-calculator/ | CALCULATOR -> Calculators | MIGRATED |  |
| 38 | IMAGE WATERMARK | imgwatermarking/index.html | image-watermark | https://tool.prompttai.com/tools/image-watermark/ | IMAGE TOOLS -> Image Tools | MIGRATED |  |
| 39 | Image Background Remover | imgbgremover/index.html | image-background-remover | https://tool.prompttai.com/tools/image-background-remover/ | IMAGE TOOLS -> Image Tools | MIGRATED |  |
| 40 | IMAGE UPSCALER | IMAGE/Image-Upscaler.html | image-upscaler | https://tool.prompttai.com/tools/image-upscaler/ | IMAGE TOOLS -> Image Tools | MIGRATED |  |
| 41 | Colour Code Converter | color-code-converter/index.php | colour-code-converter | https://tool.prompttai.com/tools/colour-code-converter/ | UTILITY TOOLS -> Utility Tools | MIGRATED |  |
| 42 | Video to MP3 | Audio & Video Tools/Video-to-MP3-Converter.html | video-to-mp3 | https://tool.prompttai.com/tools/video-to-mp3/ | Audio & Video Tools -> Audio & Video Tools | MIGRATED |  |
| 43 | MP3 ↔ WAV Converter | Audio & Video Tools/MP3-to-WAV-WAV-to-MP3-Converter.html | mp3-wav-converter | https://tool.prompttai.com/tools/mp3-wav-converter/ | Audio & Video Tools -> Audio & Video Tools | MIGRATED |  |
| 44 | MP3 Cutter | Audio & Video Tools/MP3-Cutter-Audio-Trimmer-tool.html | mp3-cutter | https://tool.prompttai.com/tools/mp3-cutter/ | Audio & Video Tools -> Audio & Video Tools | MIGRATED |  |
| 45 | Video Resizer / Cropper | Audio & Video Tools/Video-Resizer-Cropper.html | video-resizer-cropper | https://tool.prompttai.com/tools/video-resizer-cropper/ | Audio & Video Tools -> Audio & Video Tools | MIGRATED |  |
| 46 | Screen Recorder | Audio & Video Tools/Screen-Recorder.html | screen-recorder | https://tool.prompttai.com/tools/screen-recorder/ | Audio & Video Tools -> Audio & Video Tools | MIGRATED |  |
| 47 | Free Online TTS | Audio & Video Tools/Text-to-Speech.html | free-online-tts | https://tool.prompttai.com/tools/free-online-tts/ | Audio & Video Tools -> Audio & Video Tools | MIGRATED |  |
| 48 | SPLIT PDF | PDF/Splitpdf.html | split-pdf | https://tool.prompttai.com/tools/split-pdf/ | PDF TOOLS -> PDF Tools | MIGRATED |  |
| 49 | MERGE PDF | PDF/Mergepdf.html | merge-pdf | https://tool.prompttai.com/tools/merge-pdf/ | PDF TOOLS -> PDF Tools | MIGRATED |  |
| 50 | PDF TO IMAGE | PDF/Pdftoimg.html | pdf-to-image | https://tool.prompttai.com/tools/pdf-to-image/ | PDF TOOLS -> PDF Tools | MIGRATED |  |
| 51 | IMAGE TO PDF | PDF/Imgtopdf.html | image-to-pdf | https://tool.prompttai.com/tools/image-to-pdf/ | PDF TOOLS -> PDF Tools | MIGRATED |  |
| 52 | Compress PDF | PDF/Pdfcompressor.html | compress-pdf | https://tool.prompttai.com/tools/compress-pdf/ | PDF TOOLS -> PDF Tools | MIGRATED |  |
| 53 | WORD TO PDF | PDF/Wordtopdf.html | word-to-pdf | https://tool.prompttai.com/tools/word-to-pdf/ | PDF TOOLS -> PDF Tools | MIGRATED |  |
| 54 | Unlock PDF | PDF/unlockpdf.html | unlock-pdf | https://tool.prompttai.com/tools/unlock-pdf/ | PDF TOOLS -> PDF Tools | MIGRATED |  |
| 55 | PDF TO WORD | PDF/Pdftoword.html | pdf-to-word | https://tool.prompttai.com/tools/pdf-to-word/ | PDF TOOLS -> PDF Tools | MIGRATED |  |
| 56 | 1 MB PDF COMPRESSOR | HIGH COMPRESS/1mb-pdf-compress.html | 1-mb-pdf-compressor | https://tool.prompttai.com/tools/1-mb-pdf-compressor/ | HIGH COMPRESSOR -> Compression Tools | MIGRATED |  |
| 57 | 20 KB IMAGE COMPRESSOR | HIGH COMPRESS/20kb-image-compress.html | 20-kb-image-compressor | https://tool.prompttai.com/tools/20-kb-image-compressor/ | HIGH COMPRESSOR -> Compression Tools | MIGRATED |  |
| 58 | PDF TO EXCEL | PDF/Pdftoexcel.html | pdf-to-excel | https://tool.prompttai.com/tools/pdf-to-excel/ | PDF TOOLS -> PDF Tools | MIGRATED |  |
| 59 | LOCK PDF | PDF/lockpdf.html | lock-pdf | https://tool.prompttai.com/tools/lock-pdf/ | PDF TOOLS -> PDF Tools | MIGRATED |  |
| 60 | EXCEL TO PDF | PDF/exceltopdf.html | excel-to-pdf | https://tool.prompttai.com/tools/excel-to-pdf/ | PDF TOOLS -> PDF Tools | MIGRATED |  |
| 61 | Organize PDF | PDF/organize-pdf.html | organize-pdf | https://tool.prompttai.com/tools/organize-pdf/ | PDF TOOLS -> PDF Tools | MIGRATED |  |
| 62 | Repair PDF | PDF/repair-pdf.html | repair-pdf | https://tool.prompttai.com/tools/repair-pdf/ | PDF TOOLS -> PDF Tools | MIGRATED |  |
| 63 | JIRO-AI EXPLAINER | https://ai.earncolour.xyz/J1R0-AI-EXPAIN-IT/JIRO-AI-EXPLAIN-IT.html | jiro-ai-explainer | https://tool.prompttai.com/tools/jiro-ai-explainer/ | 🤖 AI Tools -> AI Tools | MIGRATED |  |
| 64 | JIRO-AI NOTES MAKER | https://ai.earncolour.xyz/notes-maker-ai.html | jiro-ai-notes-maker | https://tool.prompttai.com/tools/jiro-ai-notes-maker/ | 🤖 AI Tools -> AI Tools | MIGRATED |  |
| 65 | BLUR TOOL | IMAGE/image-background-blur.html | blur-tool | https://tool.prompttai.com/tools/blur-tool/ | IMAGE TOOLS -> Image Tools | MIGRATED |  |
| 66 | ENGLISH GRAMMER - VOICE | ppt-maker/index.html | english-grammar-voice-66 | https://tool.prompttai.com/tools/english-grammar-voice-66/ | EDUCATIONAL CONTENT -> Educational Tools | MIGRATED |  |
| 67 | ENGLISH GRAMMER - VOICE | ppt-maker/index.html | english-grammar-voice-67 | https://tool.prompttai.com/tools/english-grammar-voice-67/ | 📚 Educational Tools -> Educational Tools | MIGRATED |  |
| 68 | TENSES | Educational Tools/TENSES/index.html | tenses | https://tool.prompttai.com/tools/tenses/ | 📚 Educational Tools -> Educational Tools | MIGRATED |  |
| 69 | IMAGE SPLITTER | IMAGE/Image-Splitter.html | image-splitter | https://tool.prompttai.com/tools/image-splitter/ | IMAGE TOOLS -> Image Tools | MIGRATED |  |
| 70 | Online Date & Time Difference Calculator | Utility Tools/OnlinDate-Time-Difference-Calculator.html | online-date-time-difference-calculator | https://tool.prompttai.com/tools/online-date-time-difference-calculator/ | CALCULATOR -> Calculators | MIGRATED |  |
| 71 | AREA CALCULATOR | Educational Tools/Area-calculator.html | area-calculator | https://tool.prompttai.com/tools/area-calculator/ | CALCULATOR -> Calculators | MIGRATED |  |
| 72 | TYPING SPEED TEST | /Educational Tools/typing-speed-test.html | typing-speed-test | https://tool.prompttai.com/tools/typing-speed-test/ | 📚 Educational Tools -> Educational Tools | MIGRATED |  |
| 73 | 🛠 Remove Extra Spaces | DOCUMENTS/Remove-Extra-Spaces-Tool-html | remove-extra-spaces | https://tool.prompttai.com/tools/remove-extra-spaces/ | DOCUMENTS -> Text & Document Tools | MIGRATED |  |
| 74 | Immediate-Annuity-Calculator | Financial Calculators/Immediate-Annuity-Calculator.html | immediate-annuity-calculator | https://tool.prompttai.com/tools/immediate-annuity-calculator/ | FINANCIAL CALCULATORS -> Financial Calculators | MIGRATED |  |
| 75 | Present Value of Annuity Due Calculator | Financial Calculators/Present Value-of-Annuity-Due-Calculator.html | present-value-of-annuity-due-calculator | https://tool.prompttai.com/tools/present-value-of-annuity-due-calculator/ | FINANCIAL CALCULATORS -> Financial Calculators | MIGRATED |  |
| 76 | Present Value of Annuity Calculator | Financial Calculators/Present-Value-of-Annuity-Calculator.html | present-value-of-annuity-calculator | https://tool.prompttai.com/tools/present-value-of-annuity-calculator/ | FINANCIAL CALCULATORS -> Financial Calculators | MIGRATED |  |
| 77 | Present Value of Growing Annuity Calculator | Financial Calculators/Present-Value-of-Growing-Annuity-Calculator.html | present-value-of-growing-annuity-calculator | https://tool.prompttai.com/tools/present-value-of-growing-annuity-calculator/ | FINANCIAL CALCULATORS -> Financial Calculators | MIGRATED |  |
| 78 | PVIFA Calculator (High Precision) | Financial Calculators/PVIFA-Calculator-High-Precision.html | pvifa-calculator-high-precision | https://tool.prompttai.com/tools/pvifa-calculator-high-precision/ | FINANCIAL CALCULATORS -> Financial Calculators | MIGRATED |  |
| 79 | CPC CALCULATOR | Web Master/Cpc-calculator.html | cpc-calculator | https://tool.prompttai.com/tools/cpc-calculator/ | WEBMASTER -> Webmaster Tools | MIGRATED |  |
| 80 | CPM CALCULATOR | Web Master/Cpm-calculator.html | cpm-calculator | https://tool.prompttai.com/tools/cpm-calculator/ | WEBMASTER -> Webmaster Tools | MIGRATED |  |
| 81 | AdSense Calculator | Web Master/Adsense-calculator.html | adsense-calculator | https://tool.prompttai.com/tools/adsense-calculator/ | WEBMASTER -> Webmaster Tools | MIGRATED |  |
| 82 | At Bats per Home Run Calculato | SPORTS/ BASEBALL/At-Bats-per-Home-Run-Calculator.html | at-bats-per-home-run-calculato | https://tool.prompttai.com/tools/at-bats-per-home-run-calculato/ | SPORTS-BASEBALL -> Sports | MIGRATED |  |
| 83 | Batting Average Calculator | SPORTS/ BASEBALL/Batting-Average-Calculator.html | batting-average-calculator | https://tool.prompttai.com/tools/batting-average-calculator/ | SPORTS-BASEBALL -> Sports | MIGRATED |  |
| 84 | ERA Calculator | SPORTS/ BASEBALL/ERA-Calculator.html | era-calculator | https://tool.prompttai.com/tools/era-calculator/ | SPORTS-BASEBALL -> Sports | MIGRATED |  |
| 85 | Field Goal Percentage Calculator | SPORTS/ BASEBALL/Field-Goal-Percentage-Calculator.html | field-goal-percentage-calculator | https://tool.prompttai.com/tools/field-goal-percentage-calculator/ | SPORTS-BASEBALL -> Sports | MIGRATED |  |
| 86 | FIP Calculator | SPORTS/ BASEBALL/FIP-Calculator.html | fip-calculator | https://tool.prompttai.com/tools/fip-calculator/ | SPORTS-BASEBALL -> Sports | MIGRATED |  |
| 87 | On Base Percentage Calculator | SPORTS/ BASEBALL/On-Base-Percentage-Calculator.html | on-base-percentage-calculator | https://tool.prompttai.com/tools/on-base-percentage-calculator/ | SPORTS-BASEBALL -> Sports | MIGRATED |  |
| 88 | OPS Calculator | SPORTS/ BASEBALL/OPS-Calculator.html | ops-calculator | https://tool.prompttai.com/tools/ops-calculator/ | SPORTS-BASEBALL -> Sports | MIGRATED |  |
| 89 | Slugging Percentage Calculator | SPORTS/ BASEBALL/Slugging-Percentage-Calculator.html | slugging-percentage-calculator | https://tool.prompttai.com/tools/slugging-percentage-calculator/ | SPORTS-BASEBALL -> Sports | MIGRATED |  |
| 90 | Strikeout-to-Walk Ratio Calculator | SPORTS/ BASEBALL/Strikeout-to-Walk-Ratio-Calculator.html | strikeout-to-walk-ratio-calculator | https://tool.prompttai.com/tools/strikeout-to-walk-ratio-calculator/ | SPORTS-BASEBALL -> Sports | MIGRATED |  |
| 91 | Total Bases Calculator | SPORTS/ BASEBALL/Total-Bases-Calculator.html | total-bases-calculator | https://tool.prompttai.com/tools/total-bases-calculator/ | SPORTS-BASEBALL -> Sports | MIGRATED |  |
| 92 | WAR Calculator | SPORTS/ BASEBALL/WAR-Calculator.html | war-calculator | https://tool.prompttai.com/tools/war-calculator/ | SPORTS-BASEBALL -> Sports | MIGRATED |  |
| 93 | WHIP Calculator | SPORTS/ BASEBALL/WHIP-Calculator.html | whip-calculator | https://tool.prompttai.com/tools/whip-calculator/ | SPORTS-BASEBALL -> Sports | MIGRATED |  |
| 94 | Effective Field Goal Percentage Calculator | SPORTS/BASKETBALL/Effective-Field-Goal-Percentage-Calculator.html | effective-field-goal-percentage-calculator | https://tool.prompttai.com/tools/effective-field-goal-percentage-calculator/ | SPORTS-BASKETBALL -> Sports | MIGRATED |  |
| 95 | PER Calculator | SPORTS/BASKETBALL/PER-Calculator.html | per-calculator | https://tool.prompttai.com/tools/per-calculator/ | SPORTS-BASKETBALL -> Sports | MIGRATED |  |
| 96 | Rebound Rate Calculator | SPORTS/BASKETBALL/Rebound-Rate-Calculator.html | rebound-rate-calculator | https://tool.prompttai.com/tools/rebound-rate-calculator/ | SPORTS-BASKETBALL -> Sports | MIGRATED |  |
| 97 | True Shooting Percentage Calculator | SPORTS/BASKETBALL/True-Shooting-Percentage-Calculator.html | true-shooting-percentage-calculator | https://tool.prompttai.com/tools/true-shooting-percentage-calculator/ | SPORTS-BASKETBALL -> Sports | MIGRATED |  |
| 98 | Usage Rate Calculator | SPORTS/BASKETBALL/Usage-Rate-Calculator.html | usage-rate-calculator | https://tool.prompttai.com/tools/usage-rate-calculator/ | SPORTS-BASKETBALL -> Sports | MIGRATED |  |
| 99 | Batting Average Calculator | SPORTS/CRICKET/Batting-Average-Calculator.html | batting-average-calculator-cricket | https://tool.prompttai.com/tools/batting-average-calculator-cricket/ | SPORTS-CRICKET -> Sports | MIGRATED |  |
| 100 | Strike Rate Calculator | SPORTS/CRICKET/Strike-Rate-Calculator.html | strike-rate-calculator | https://tool.prompttai.com/tools/strike-rate-calculator/ | SPORTS-CRICKET -> Sports | MIGRATED |  |
| 101 | Bowling Average Calculator | SPORTS/CRICKET/Bowling-Average-Calculator.html | bowling-average-calculator | https://tool.prompttai.com/tools/bowling-average-calculator/ | SPORTS-CRICKET -> Sports | MIGRATED |  |
| 102 | Economy Rate Calculator | SPORTS/CRICKET/Economy-Rate-Calculator.html | economy-rate-calculator | https://tool.prompttai.com/tools/economy-rate-calculator/ | SPORTS-CRICKET -> Sports | MIGRATED |  |
| 103 | Bowling Strike Rate Calculator | SPORTS/CRICKET/Bowling-Strike-Rate-Calculator.html | bowling-strike-rate-calculator | https://tool.prompttai.com/tools/bowling-strike-rate-calculator/ | SPORTS-CRICKET -> Sports | MIGRATED |  |
| 104 | Run Rate Calculator | SPORTS/CRICKET/Run-Rate-Calculator.html | run-rate-calculator | https://tool.prompttai.com/tools/run-rate-calculator/ | SPORTS-CRICKET -> Sports | MIGRATED |  |
| 105 | Required Run Rate Calculator | SPORTS/CRICKET/Required-Run-Rate-Calculator.html | required-run-rate-calculator | https://tool.prompttai.com/tools/required-run-rate-calculator/ | SPORTS-CRICKET -> Sports | MIGRATED |  |
| 106 | Fantasy Points Calculator | SPORTS/CRICKET/Fantasy-Points-Calculator.html | fantasy-points-calculator | https://tool.prompttai.com/tools/fantasy-points-calculator/ | SPORTS-CRICKET -> Sports | MIGRATED |  |
| 107 | Conception Date Calculator | HEALTH AND FITNESS/Conception-Date-Calculator.html | conception-date-calculator | https://tool.prompttai.com/tools/conception-date-calculator/ | HEALTH AND FITNESS -> Health & Fitness | MIGRATED |  |
| 108 | Fertility Calculator | HEALTH AND FITNESS/Fertility-Calculator.html | fertility-calculator | https://tool.prompttai.com/tools/fertility-calculator/ | HEALTH AND FITNESS -> Health & Fitness | MIGRATED |  |
| 109 | Menstrual Cycle Calculator | HEALTH AND FITNESS/Menstrual-Cycle-Calculator.html | menstrual-cycle-calculator | https://tool.prompttai.com/tools/menstrual-cycle-calculator/ | HEALTH AND FITNESS -> Health & Fitness | MIGRATED |  |
| 110 | Menstrual Cycle Length Calculator | HEALTH AND FITNESS/Menstrual-Cycle-Length-Calculator.html | menstrual-cycle-length-calculator | https://tool.prompttai.com/tools/menstrual-cycle-length-calculator/ | HEALTH AND FITNESS -> Health & Fitness | MIGRATED |  |
| 111 | Ovulation-Calculator | HEALTH AND FITNESS/Ovulation-Calculator.html | ovulation-calculator | https://tool.prompttai.com/tools/ovulation-calculator/ | HEALTH AND FITNESS -> Health & Fitness | MIGRATED |  |
| 112 | Bench Press Calculator | HEALTH AND FITNESS/Bench-Press-Calculator.html | bench-press-calculator | https://tool.prompttai.com/tools/bench-press-calculator/ | HEALTH AND FITNESS -> Health & Fitness | MIGRATED |  |
| 113 | BMR Calculator | HEALTH AND FITNESS/BMR-Calculator.html | bmr-calculator | https://tool.prompttai.com/tools/bmr-calculator/ | HEALTH AND FITNESS -> Health & Fitness | MIGRATED |  |
| 114 | Body Fat Percentage Calculator | HEALTH AND FITNESS/Body-Fat-Percentage-Calculator.html | body-fat-percentage-calculator | https://tool.prompttai.com/tools/body-fat-percentage-calculator/ | HEALTH AND FITNESS -> Health & Fitness | MIGRATED |  |
| 115 | Calorie Burned Calculator | HEALTH AND FITNESS/Calorie-Burned-Calculator.html | calorie-burned-calculator | https://tool.prompttai.com/tools/calorie-burned-calculator/ | HEALTH AND FITNESS -> Health & Fitness | MIGRATED |  |
| 116 | Calorie Deficit Calculator | HEALTH AND FITNESS/Calorie-Deficit-Calculator.html | calorie-deficit-calculator | https://tool.prompttai.com/tools/calorie-deficit-calculator/ | HEALTH AND FITNESS -> Health & Fitness | MIGRATED |  |
| 117 | Max Heart Rate Calculator | HEALTH AND FITNESS/Max-Heart-Rate-Calculator.html | max-heart-rate-calculator | https://tool.prompttai.com/tools/max-heart-rate-calculator/ | HEALTH AND FITNESS -> Health & Fitness | MIGRATED |  |
| 118 | One Rep Max (1RM) Calculator | HEALTH AND FITNESS/One-Rep-Max-Calculator.html | one-rep-max-1rm-calculator | https://tool.prompttai.com/tools/one-rep-max-1rm-calculator/ | HEALTH AND FITNESS -> Health & Fitness | MIGRATED |  |
| 119 | Running Pace Calculator | HEALTH AND FITNESS/Running-Pace-Calculator.html | running-pace-calculator | https://tool.prompttai.com/tools/running-pace-calculator/ | HEALTH AND FITNESS -> Health & Fitness | MIGRATED |  |
| 120 | Target Heart Rate Calculator | HEALTH AND FITNESS/Target-Heart-Rate-Calculator.html | target-heart-rate-calculator | https://tool.prompttai.com/tools/target-heart-rate-calculator/ | HEALTH AND FITNESS -> Health & Fitness | MIGRATED |  |
| 121 | Am I Overweight | HEALTH AND FITNESS/Am-I-Overweight.html | am-i-overweight | https://tool.prompttai.com/tools/am-i-overweight/ | HEALTH AND FITNESS -> Health & Fitness | MIGRATED |  |
| 122 | Am I Underweight? | HEALTH AND FITNESS/Am-I-Underweight.html | am-i-underweight | https://tool.prompttai.com/tools/am-i-underweight/ | HEALTH AND FITNESS -> Health & Fitness | MIGRATED |  |
| 123 | BMI Calculator | HEALTH AND FITNESS/BMI-Calculator.html | bmi-calculator | https://tool.prompttai.com/tools/bmi-calculator/ | HEALTH AND FITNESS -> Health & Fitness | MIGRATED |  |
| 124 | How Much Should I Weigh? | HEALTH AND FITNESS/How-Much-Should-I-Weigh.html | how-much-should-i-weigh | https://tool.prompttai.com/tools/how-much-should-i-weigh/ | HEALTH AND FITNESS -> Health & Fitness | MIGRATED |  |
| 125 | Waist to Hip Ratio Calculator | HEALTH AND FITNESS/Waist-to-Hip-Ratio-Calculator.html | waist-to-hip-ratio-calculator | https://tool.prompttai.com/tools/waist-to-hip-ratio-calculator/ | HEALTH AND FITNESS -> Health & Fitness | MIGRATED |  |
| 126 | What Should I Weigh? | HEALTH AND FITNESS/What-Should-I-Weigh.html | what-should-i-weigh | https://tool.prompttai.com/tools/what-should-i-weigh/ | HEALTH AND FITNESS -> Health & Fitness | MIGRATED |  |
| 127 | Bra Size Calculator | HEALTH AND FITNESS/bra-size-calculator.html | bra-size-calculator | https://tool.prompttai.com/tools/bra-size-calculator/ | HEALTH AND FITNESS -> Health & Fitness | MIGRATED |  |
| 128 | Waist-to-Height Ratio (WHtR) Calculator | HEALTH AND FITNESS/WHtR-Calculator.html | waist-to-height-ratio-whtr-calculator | https://tool.prompttai.com/tools/waist-to-height-ratio-whtr-calculator/ | HEALTH AND FITNESS -> Health & Fitness | MIGRATED |  |
| 129 | Due Date Calculator - Find out when your baby is due. | HEALTH AND FITNESS/Due-Date-Calculator.html | due-date-calculator-find-out-when-your-baby-is-due | https://tool.prompttai.com/tools/due-date-calculator-find-out-when-your-baby-is-due/ | HEALTH AND FITNESS -> Health & Fitness | MIGRATED |  |
| 130 | How Many Weeks Pregnant Am I? | HEALTH AND FITNESS/How-Many-Weeks-Pregnant-Am-I.html | how-many-weeks-pregnant-am-i | https://tool.prompttai.com/tools/how-many-weeks-pregnant-am-i/ | HEALTH AND FITNESS -> Health & Fitness | MIGRATED |  |
| 131 | Pregnancy Calendar - Create your own daily pregnancy calendar. | HEALTH AND FITNESS/Pregnancy-Calendar.html | pregnancy-calendar-create-your-own-daily-pregnancy-calendar | https://tool.prompttai.com/tools/pregnancy-calendar-create-your-own-daily-pregnancy-calendar/ | HEALTH AND FITNESS -> Health & Fitness | MIGRATED |  |
| 132 | Bond Equivalent Yield Calculator | Financial Calculators/Bond-Equivalent-Yield-Calculator.html | bond-equivalent-yield-calculator | https://tool.prompttai.com/tools/bond-equivalent-yield-calculator/ | FINANCIAL CALCULATORS -> Financial Calculators | MIGRATED |  |
| 133 | Bond Yield Calculator | Financial Calculators/Bond-Yield-Calculator.html | bond-yield-calculator | https://tool.prompttai.com/tools/bond-yield-calculator/ | FINANCIAL CALCULATORS -> Financial Calculators | MIGRATED |  |
| 134 | Bond Yield to Maturity Calculator | Financial Calculators/Bond-Yield-to-Maturity-Calculator.html | bond-yield-to-maturity-calculator | https://tool.prompttai.com/tools/bond-yield-to-maturity-calculator/ | FINANCIAL CALCULATORS -> Financial Calculators | MIGRATED |  |
| 135 | Zero Coupon Bond Calculator | Financial Calculators/Zero-Coupon-Bond-Calculator.html | zero-coupon-bond-calculator | https://tool.prompttai.com/tools/zero-coupon-bond-calculator/ | FINANCIAL CALCULATORS -> Financial Calculators | MIGRATED |  |
| 136 | Car Loan EMI Calculator | Financial Calculators/car-loan-calculator.html | car-loan-emi-calculator | https://tool.prompttai.com/tools/car-loan-emi-calculator/ | FINANCIAL CALCULATORS -> Financial Calculators | MIGRATED |  |
| 137 | Home Loan EMI Calculator | Financial Calculators/home-loan-calculator.html | home-loan-emi-calculator | https://tool.prompttai.com/tools/home-loan-emi-calculator/ | FINANCIAL CALCULATORS -> Financial Calculators | MIGRATED |  |
| 138 | Home Equity Loan Calculator | Financial Calculators/home-equity-loan-calculator.html | home-equity-loan-calculator | https://tool.prompttai.com/tools/home-equity-loan-calculator/ | FINANCIAL CALCULATORS -> Financial Calculators | MIGRATED |  |
| 139 | Reverse Mortgage Calculator | Financial Calculators/Reverse-mortgage-calculator.html | reverse-mortgage-calculator | https://tool.prompttai.com/tools/reverse-mortgage-calculator/ | FINANCIAL CALCULATORS -> Financial Calculators | MIGRATED |  |
| 140 | Personal Loan EMI Calculator | Financial Calculators/Personal-Loan-Calculator.html | personal-loan-emi-calculator | https://tool.prompttai.com/tools/personal-loan-emi-calculator/ | FINANCIAL CALCULATORS -> Financial Calculators | MIGRATED |  |
| 141 | Retirement Calculator | Financial Calculators/retirement-calculator.html | retirement-calculator | https://tool.prompttai.com/tools/retirement-calculator/ | FINANCIAL CALCULATORS -> Financial Calculators | MIGRATED |  |
| 142 | Amortization Calculator | Financial Calculators/amortization-calculator.html | amortization-calculator | https://tool.prompttai.com/tools/amortization-calculator/ | FINANCIAL CALCULATORS -> Financial Calculators | MIGRATED |  |
| 143 | Borrowing Power Calculator | Financial Calculators/borrowing-power-calculator.html | borrowing-power-calculator | https://tool.prompttai.com/tools/borrowing-power-calculator/ | FINANCIAL CALCULATORS -> Financial Calculators | MIGRATED |  |
| 144 | Mutual Fund Calculator | Financial Calculators/mutual-fund-calculator.html | mutual-fund-calculator | https://tool.prompttai.com/tools/mutual-fund-calculator/ | FINANCIAL CALCULATORS -> Financial Calculators | MIGRATED |  |
| 145 | APY Calculator | Financial Calculators/apy-calculator.html | apy-calculator | https://tool.prompttai.com/tools/apy-calculator/ | FINANCIAL CALCULATORS -> Financial Calculators | MIGRATED |  |
| 146 | Pension Calculator | Financial Calculators/pension-calculator.html | pension-calculator | https://tool.prompttai.com/tools/pension-calculator/ | FINANCIAL CALCULATORS -> Financial Calculators | MIGRATED |  |
| 147 | Meta Tag Generator | Web Master/Meta-tag-generator.html | meta-tag-generator | https://tool.prompttai.com/tools/meta-tag-generator/ | WEBMASTER -> Webmaster Tools | MIGRATED |  |
| 148 | FIND AND REPLACE TEXT | DOCUMENTS/find-replace-text.html | find-and-replace-text | https://tool.prompttai.com/tools/find-and-replace-text/ | DOCUMENTS -> Text & Document Tools | MIGRATED |  |
| 149 | Create Transparent Images | IMAGE/transparent-image.html | create-transparent-images | https://tool.prompttai.com/tools/create-transparent-images/ | IMAGE TOOLS -> Image Tools | MIGRATED |  |
| 150 | Social Media Image Resizer | MAGE/social-media-image-resizer.html | social-media-image-resizer | https://tool.prompttai.com/tools/social-media-image-resizer/ | IMAGE TOOLS -> Image Tools | MIGRATED |  |
| 151 | PNG Color Replacer | IMAGE/png-color-replacer.html | png-color-replacer | https://tool.prompttai.com/tools/png-color-replacer/ | IMAGE TOOLS -> Image Tools | MIGRATED |  |
| 152 | PNG Color Tone Changer | IMAGE/png-color-tone-changer.html | png-color-tone-changer | https://tool.prompttai.com/tools/png-color-tone-changer/ | IMAGE TOOLS -> Image Tools | MIGRATED |  |
| 153 | Add Date to Photos — Bulk | IMAGE/add-date-bulk.html | add-date-to-photos-bulk | https://tool.prompttai.com/tools/add-date-to-photos-bulk/ | IMAGE TOOLS -> Image Tools | MIGRATED |  |
| 154 | Domain Availability Checker | Web Master/domain-availability-checker/index.php | domain-availability-checker | https://tool.prompttai.com/tools/domain-availability-checker/ | WEBMASTER -> Webmaster Tools | MIGRATED |  |
| 155 | Digital Diary App | Utility Tools/diary-app/index.php | digital-diary-app | https://tool.prompttai.com/tools/digital-diary-app/ | UTILITY TOOLS -> Utility Tools | MIGRATED |  |
| 156 | Text Hashing Tool | Web Master/text-hashing-tool/index.html | text-hashing-tool | https://tool.prompttai.com/tools/text-hashing-tool/ | WEBMASTER -> Webmaster Tools | MIGRATED |  |
| 157 | Virtual Drum Kit | Fun/virtual-drum-kit/index.html | virtual-drum-kit | https://tool.prompttai.com/tools/virtual-drum-kit/ | FUN -> Fun & Games | MIGRATED |  |
| 158 | Universal Length Converter | Educational Tools/universal-length-converter.html | universal-length-converter | https://tool.prompttai.com/tools/universal-length-converter/ | CALCULATOR -> Calculators | MIGRATED |  |
| 159 | SpeedTest Pro | Website Performance Analyzer | Web Master/website-speed-tester/index.php | speedtest-pro-website-performance-analyzer | https://tool.prompttai.com/tools/speedtest-pro-website-performance-analyzer/ | WEBMASTER -> Webmaster Tools | MIGRATED |  |
| 160 | FLIP A COIN | Fun/flip-coin.html | flip-a-coin | https://tool.prompttai.com/tools/flip-a-coin/ | FUN -> Fun & Games | MIGRATED |  |
| 161 | ID Photo Print Layout | IMAGE/id-photo-layout.html | id-photo-print-layout | https://tool.prompttai.com/tools/id-photo-print-layout/ | IMAGE TOOLS -> Image Tools | MIGRATED |  |
| 162 | Online Geo Tag Camera | Audio & Video Tools/geo-tag-camera.html | online-geo-tag-camera | https://tool.prompttai.com/tools/online-geo-tag-camera/ | Audio & Video Tools -> Audio & Video Tools | MIGRATED |  |
| 163 | Crypto Tax Estimator Canada | Crypto/Cryptocurrency_Tax_Estimator_Canada.html | crypto-tax-estimator-canada | https://tool.prompttai.com/tools/crypto-tax-estimator-canada/ | CRYPTO TAX CALCULATOR -> Financial Calculators | MIGRATED |  |
| 164 | Crypto Tax Estimator UK | Cryptocurrency_Tax_UK.html | crypto-tax-estimator-uk | https://tool.prompttai.com/tools/crypto-tax-estimator-uk/ | CRYPTO TAX CALCULATOR -> Financial Calculators | MIGRATED |  |
| 165 | Crypto Tax Estimator USA | Crypto/Cryptocurrency_Tax_Estimator_USA.html | crypto-tax-estimator-usa | https://tool.prompttai.com/tools/crypto-tax-estimator-usa/ | CRYPTO TAX CALCULATOR -> Financial Calculators | MIGRATED |  |
| 166 | Crypto Tax Estimator INDIA | Crypto/India_crypto_tax_calculator.html | crypto-tax-estimator-india | https://tool.prompttai.com/tools/crypto-tax-estimator-india/ | CRYPTO TAX CALCULATOR -> Financial Calculators | MIGRATED |  |
| 167 | Crypto Tax Calculator Germany | Crypto/Crypto_Tax_Calculator_Germany.html | crypto-tax-calculator-germany | https://tool.prompttai.com/tools/crypto-tax-calculator-germany/ | CRYPTO TAX CALCULATOR -> Financial Calculators | MIGRATED |  |
| 168 | Crypto Tax Calculator Japan | Crypto/Crypto_Tax_Calculator_Japan.html | crypto-tax-calculator-japan | https://tool.prompttai.com/tools/crypto-tax-calculator-japan/ | CRYPTO TAX CALCULATOR -> Financial Calculators | MIGRATED |  |
| 169 | Crypto Tax Calculator France | Crypto/Crypto_Tax_Estimator_France.html | crypto-tax-calculator-france | https://tool.prompttai.com/tools/crypto-tax-calculator-france/ | CRYPTO TAX CALCULATOR -> Financial Calculators | MIGRATED |  |
| 170 | Crypto Tax Calculator Italy | Crypto/Crypto_Tax_Calculator_Italy.html | crypto-tax-calculator-italy | https://tool.prompttai.com/tools/crypto-tax-calculator-italy/ | CRYPTO TAX CALCULATOR -> Financial Calculators | MIGRATED |  |
| 171 | Crypto Tax Calculator Brazil | Crypto/Crypto_Tax_Calculator_Brazil.html | crypto-tax-calculator-brazil | https://tool.prompttai.com/tools/crypto-tax-calculator-brazil/ | CRYPTO TAX CALCULATOR -> Financial Calculators | MIGRATED |  |
| 172 | Crypto Tax Calculator South_Korea | Crypto/Crypto_Tax_Calculator_South_Korea.html | crypto-tax-calculator-south-korea | https://tool.prompttai.com/tools/crypto-tax-calculator-south-korea/ | CRYPTO TAX CALCULATOR -> Financial Calculators | MIGRATED |  |
| 173 | Cryptocurrency Tax Estimator Spain | Crypto/Crypto_Tax_Estimator_Spain.html | cryptocurrency-tax-estimator-spain | https://tool.prompttai.com/tools/cryptocurrency-tax-estimator-spain/ | CRYPTO TAX CALCULATOR -> Financial Calculators | MIGRATED |  |
| 174 | Cryptocurrency Tax Estimator  Australia | Crypto/Crypto_Tax_Estimator_Australia.html | cryptocurrency-tax-estimator-australia | https://tool.prompttai.com/tools/cryptocurrency-tax-estimator-australia/ | CRYPTO TAX CALCULATOR -> Financial Calculators | MIGRATED |  |
| 175 | Crypto Tax Estimator Russia | Crypto/Crypto_Tax_Calculator_Russia.html | crypto-tax-estimator-russia | https://tool.prompttai.com/tools/crypto-tax-estimator-russia/ | CRYPTO TAX CALCULATOR -> Financial Calculators | MIGRATED |  |
| 176 | Crypto Tax Calculator Mexico | Crypto/Crypto_Tax_Calculator_Mexico.html | crypto-tax-calculator-mexico | https://tool.prompttai.com/tools/crypto-tax-calculator-mexico/ | CRYPTO TAX CALCULATOR -> Financial Calculators | MIGRATED |  |
| 177 | Crypto Tax Calculator Netherlands | Crypto/Crypto_Tax_Calculator_Netherlands.html | crypto-tax-calculator-netherlands | https://tool.prompttai.com/tools/crypto-tax-calculator-netherlands/ | CRYPTO TAX CALCULATOR -> Financial Calculators | MIGRATED |  |
| 178 | Crypto Tax Calculator Indonesia | Crypto/Crypto_Tax_Calculator_Indonesia.html | crypto-tax-calculator-indonesia | https://tool.prompttai.com/tools/crypto-tax-calculator-indonesia/ | CRYPTO TAX CALCULATOR -> Financial Calculators | MIGRATED |  |
| 179 | Crypto Tax Calculator Saudi Arabia | Crypto/Crypto_Tax_Calculator_Saudi_Arabia.html | crypto-tax-calculator-saudi-arabia | https://tool.prompttai.com/tools/crypto-tax-calculator-saudi-arabia/ | CRYPTO TAX CALCULATOR -> Financial Calculators | MIGRATED |  |
| 180 | Crypto Tax Calculator Turkey | Crypto/Crypto_Tax_Calculator_Turkey.html | crypto-tax-calculator-turkey | https://tool.prompttai.com/tools/crypto-tax-calculator-turkey/ | CRYPTO TAX CALCULATOR -> Financial Calculators | MIGRATED |  |
| 181 | Crypto Tax Estimator Isreal | Crypto/Crypto_Tax_Calculator_Ireland.html | crypto-tax-estimator-isreal | https://tool.prompttai.com/tools/crypto-tax-estimator-isreal/ | CRYPTO TAX CALCULATOR -> Financial Calculators | MIGRATED |  |

## Summary Updated 2026-10-07
- Total tools: 181
- Migrated in this batch: 171
- External wrappers: 10
- Total migrated: 181 (100%)
- Needs manual review: 0
- Duplicates merged: 2 (english-grammer-voice, batting-average-calculator)

All tools now have unified UI, usage gating, SEO metadata, and are stored in /tools/{slug}/ with metadata.json