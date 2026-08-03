<?php
/**
 * NursesPro Academy - Seed: Topics & Subtopics
 *
 * Populates the Course/Module -> Topic -> Subtopic hierarchy with the real
 * DNE curriculum content. Idempotent: safe to re-run — skips any topic that
 * already exists (matched by module_id + title) and any subtopic that already
 * exists (matched by topic_id + title).
 *
 * Mapping notes (module headings in the source document -> existing module codes):
 * - "Pediatric Nursing II" -> DNE 211 (Paediatric Nursing III) — same subject, the
 *   source numbering doesn't match our seeded module numeral; content wins.
 * - "Mental Health Nursing (II)" and "Pharmacology (III)" both -> DNE 114
 *   (already titled "Mental Health Nursing II & Pharmacology II" — a combined module).
 * - "Primary Health Care (PHC)" and "Community Based Health Care (CBHC)" both -> DNE 121.
 * - "Applied Research" and "Teaching Methodology" both -> DNE 122.
 * - "Disaster Management" and "Occupational Health and Safety" both -> DNE 124.
 * - "Gynaecology (II)" and "Reproductive Health (II)" both -> DNE 212.
 * - "Health Service Management" and "Entrepreneurship" both -> DNE 213.
 * - A block of clearly-pediatric topics (nervous/endocrine/urinary/skin/eye/ear/IMCI in
 *   children) was pasted under the "Occupational Health and Safety" heading in the
 *   source — that's almost certainly a document-structure mix-up, so those topics are
 *   seeded under DNE 211 (Paediatric Nursing) instead of DNE 124, where the content
 *   actually belongs.
 *
 * Run: php sql/seed_topics.php
 */

require_once __DIR__ . '/../config/db.php';
$pdo = db();

// Each module: [ 'Topic title' => [ [code_or_null, 'Subtopic title'], ... ], ... ]
$DATA = [

  'DNE 111' => [
    'Applying nursing process to the management of patients' => [
      [null, 'Apply nursing process in the management of patients with conditions of the reproductive system'],
      [null, 'Apply nursing process in the management of patients with conditions of the endocrine system'],
      [null, 'Apply nursing process in the management of patients with conditions of the nervous system'],
      [null, 'Apply nursing process in the management of patients with conditions of the renal system'],
      [null, 'Apply nursing process in the management of patients with conditions of the musculo-skeletal system'],
      [null, 'Apply nursing process in the management of patients with conditions of the lymphatic system'],
      [null, 'Apply nursing process in the management of patients with conditions of the digestive system'],
      [null, 'Apply nursing process in the management of patients with conditions of the respiratory system'],
      [null, 'Apply nursing process in the management of patients with conditions of the cardiovascular system'],
      [null, 'Apply nursing process in the management of patients with conditions of the skin'],
      [null, 'Apply nursing process in the management of patients with conditions of the eye'],
      [null, 'Apply nursing process in the management of patients with conditions of the ear, nose and throat (ENT)'],
    ],
    'Perform specialized nursing care' => [
      [null, 'Perform Shortening and removal of drains'],
      [null, 'Perform Colostomy Care'],
      [null, 'Prepare for Abdominis Paracentesis (Abdominal Tapping)'],
      [null, 'Prepare for Lumbar Puncture'],
      [null, 'Perform Gastrostomy Feeding'],
      [null, 'Carry out gastric Lavage'],
      [null, 'Perform Tracheostomy Care'],
      [null, 'Ophthalmology (Eye care, Pre & Post Operative care, Charts)'],
      [null, "Care of the patients' ears"],
      [null, 'Peri-Operative care'],
      [null, 'Wound dressing'],
    ],
    'Conditions affecting the nervous system' => [
      ['4.8.1', 'Applied anatomy and Physiology of the nervous system'],
      ['4.8.2', 'Trigeminal neuralgia'],
      ['4.8.3', "Bell's palsy"],
      ['4.8.4', "Parkinson's disease"],
      [null, 'Spinal cord compression'],
      [null, 'Transverse Myelitis'],
      [null, 'Sub arachnoid haemorrhage and intra cranial aneurysm'],
      [null, 'General Paralysis of Insane'],
    ],
    'Medical conditions affecting the endocrine system' => [
      ['4.9.1', 'Applied anatomy and physiology of the endocrine system'],
      ['4.9.2', 'Acromegaly/Gigantism (Hyperpituitarism)'],
      ['4.9.3', 'Dwarfism (Panhypopituitarism)'],
      ['4.9.4', "Addison's disease (Adrenal insufficiency)"],
      ['4.9.5', 'Pheochromocytoma'],
      ['4.9.6', "Cushing's syndrome"],
      ['4.9.7', 'Hyperaldosteronism'],
      ['4.9.8', 'Thyrotoxicosis'],
      ['4.9.8', 'Hyperthyroidism'],
      ['4.9.8', 'Hyperparathyroidism & Hypoparathyroidism'],
      ['4.9.9', 'Diabetes Mellitus Type 1'],
      ['4.9.9', 'Diabetes Mellitus Type 2'],
    ],
    'Medical diseases affecting the renal system' => [
      ['4.10.0', 'Anatomy and Physiology of the Renal System'],
      ['4.10.1', 'Urinary tract infections'],
      ['4.10.2', 'Polycystic Kidney disease (PKD)'],
      ['4.10.3', 'Kidney stones'],
    ],
    'Conditions of the lymphatic system' => [
      ['4.11.1', 'Anatomy and Physiology of the Lymphatic System'],
      ['4.11.2', 'Lymphedema'],
      ['4.11.3', 'Lymphangitis'],
      ['4.11.4', "Hodgkin's disease"],
    ],
    'Conditions of the Musculo-skeletal system' => [
      ['4.12.0', 'Anatomy and Physiology of the Musculo-skeletal System'],
      ['4.12.1', 'Tendonitis'],
      ['4.12.2', 'Rheumatoid Arthritis'],
      ['4.12.3', 'Osteoarthritis'],
      ['4.12.4', 'Gout'],
      ['4.12.5', 'Bursitis'],
      ['4.12.6', 'Ankylosing Spondylitis'],
      ['4.12.7', 'Systemic Lupus Erythematosus (SLE)'],
      ['4.12.8', 'Osteoporosis'],
      ['4.12.9', "Paget's disease"],
      ['4.12.10', 'Dermatitis'],
      ['4.12.11', 'Acne vulgaris'],
      ['4.12.12', 'Psoriasis'],
      ['4.12.13', 'Herpes zoster'],
      ['4.12.14', 'Onychomycosis'],
    ],
  ],

  'DNE 113' => [
    'Surgical conditions of the Ear, Nose, and Throat (ENT)' => [
      ['4.6.1', 'Common tumors of ear nose and throat (ENT)'],
      ['4.6.2', 'Adenitis'],
      ['4.6.3', 'Nasal Polyps'],
      ['4.6.4', 'Peritonsillar'],
      ['4.6.5', 'Tonsillitis'],
      ['4.6.6', 'Otitis Media'],
      ['4.6.7', 'Adenoid Hypertrophy'],
      ['4.6.8', 'Furunculosis'],
      ['4.6.9', 'Foreign bodies of Ear, Nose'],
      ['4.6.9', 'Foreign bodies of Throat'],
      ['4.6.10', 'Epistaxis (Nose Bleeding)'],
    ],
    'Conditions of the eye' => [
      [null, 'Anatomy Of the Eye'],
      ['4.7.1', 'Conjunctivitis'],
      ['4.7.2', 'Trachoma'],
      ['4.7.3', 'Stye'],
      ['4.7.4', 'Foreign body in the Eye'],
      ['4.7.5', 'Eye Trauma'],
      ['4.7.6', 'Exophthalmos / Proptosis'],
      ['4.7.7', 'Glaucoma'],
      ['4.7.8', 'Corneal Ulcers'],
      ['4.7.9', 'Cataract'],
    ],
  ],

  'DNE 211' => [
    'Pediatric condition of the respiratory system' => [
      ['4.17.0', 'Resuscitation'],
      ['4.17.1', 'Respiratory distress syndrome'],
      ['4.17.2', 'Broncho pulmonary dysplasia/ chronic lung disease'],
      ['4.17.3', 'Meconium Aspiration Syndrome'],
      ['4.17.4', 'Pulmonary hemorrhage'],
      ['4.17.5', 'Apnea'],
      ['4.17.6', 'Pneumonia'],
      ['4.17.7', 'Asthma'],
    ],
    'Pediatric conditions of the cardio vascular system' => [
      ['4.18.1', 'Sickle cell disease'],
      ['4.18.2', 'Pericarditis'],
      ['4.18.3', 'Rheumatic heart disease'],
    ],
    'Neurological disorders in children' => [
      ['4.19.1', 'Congenital Toxoplasmosis'],
      ['4.19.2', 'Intracranial Hemorrhage'],
      ['4.19.3', 'Hypoxic Ischemic encephalopathy and its classifications'],
    ],
    'Genital urinary conditions in children' => [
      ['4.20.1', 'Acute Glomerulonephritis'],
      ['4.20.2', 'Nephrotic Syndrome'],
      ['4.20.3', 'Nephritic Syndrome'],
      ['4.20.4', 'Hydrocele'],
    ],
    'Bone conditions' => [
      ['4.21.1', 'Fractures'],
      ['4.21.2', 'Osteopenia of Prematurity (metabolic bone diseases)'],
      ['4.21.3', 'Osteomyelitis'],
      ['4.21.4', 'Osteogenesis Imperfecta'],
    ],
    'Managing children living with HIV /AIDS' => [
      ['4.22.1', 'Introduction to HIV/AIDs in children'],
      ['4.22.2', 'Clinical manifestation of HIV / AIDS in Children'],
      ['4.22.3', 'Opportunistic Infections in Children'],
      ['4.22.4', 'Diagnostic Measures'],
      ['4.22.4', 'Treatment of HIV/AIDS in Children (ARV therapy)'],
      ['4.22.5', 'Prevention and Control of HIV/AIDS'],
      ['4.22.6', 'Counseling in HIV/AIDS'],
    ],
    // Note: these were pasted under "Occupational Health and Safety" in the source
    // document, but are clearly paediatric content — seeded here instead.
    'Medical conditions affecting the nervous system (children)' => [
      [null, 'Hemophilus influenza'],
      [null, 'Meningitis'],
      [null, 'Intersexual disabilities'],
      [null, 'Seizures disorders'],
      [null, 'Cerebral palsy'],
    ],
    'Endocrine disorders affecting the children' => [
      [null, 'Diabetes Mellitus & Diabetic Keto Acidosis'],
      [null, 'Thyrotoxicosis'],
      [null, 'Precocious Puberty'],
    ],
    'Urinary disorders affecting the children' => [
      [null, 'Nephrotic syndrome'],
      [null, 'Nephritic syndrome'],
      [null, 'Hydrocele'],
    ],
    'Integumentary disorders of the skin (children)' => [
      [null, 'Atopic dermatitis'],
      [null, 'Eczema'],
      [null, 'Skin allergies'],
      [null, 'Plant allergies'],
      [null, 'Stings and bites'],
    ],
    'Eye conditions (children)' => [
      [null, 'Glaucoma'],
      [null, 'Visual impairment'],
      [null, 'Congenital Cataract'],
      [null, 'Strabismus'],
      [null, 'Eye injuries in children'],
      [null, 'Foreign bodies in the eye'],
      [null, 'Eye infections'],
      [null, "Care of a child under-going eye surgery"],
    ],
    'Conditions of the ear and Nose (children)' => [
      [null, 'Hearing impairment'],
      [null, 'Removal of foreign bodies from the ear and nose'],
      [null, "Reyes syndrome"],
    ],
    'Integrated Management of Childhood illnesses (IMCI)' => [
      [null, 'IMCI strategy in health care'],
      [null, 'General danger signs'],
      [null, 'Assess and classify a sick child 2 months to 5 years'],
      [null, 'Treat the Child'],
      [null, 'Assess and classify a sick young infant 0-2 months'],
      [null, 'Manage HIV/AIDS using IMCI approach'],
    ],
  ],

  'DNE 114' => [
    'Psychiatric emergencies' => [
      [null, 'Introduction'],
      [null, 'Suicide and suicidal behaviour'],
      [null, 'Violence and aggression of patients / clients'],
      [null, 'Panic attacks/disorders'],
      [null, 'Catatonic stupor syndrome in schizophrenic patients'],
      [null, 'Status epilepticus'],
      [null, 'Epilepsy'],
    ],
    'Legal issues in psychiatry' => [
      [null, 'Law and Mental illness'],
      [null, "Patient/client's rights"],
      [null, 'Standards of Care'],
      [null, 'Mental Treatment Act'],
    ],
    'Mental health disorders in children' => [
      [null, 'Autism'],
      [null, 'Attention deficit hyperactive disorders'],
      [null, 'Mood disorders'],
      [null, 'Bipolar Affective Disorder'],
      [null, 'Suicide'],
      [null, 'Anxiety Disorders'],
      [null, 'Post-traumatic stress disorder'],
      [null, 'Substance Abuse'],
      [null, 'Eating disorders'],
      [null, 'Mental Retardation now Intellectual Disability'],
    ],
    'Drugs used in the reproductive system' => [
      [null, 'Gonadotropin drugs (Subfertility, Ovulation Induction)'],
      [null, 'Infertility drugs'],
      [null, 'Androgens, Antiandrogens & Anabolic Steroids'],
      [null, 'BPH & BPH Drugs'],
      [null, 'Erectile Dysfunction Medications'],
      [null, 'Contraceptives'],
      [null, 'Pregnancy, Labour, and Puerperium Drugs'],
    ],
    'Immunological drugs' => [
      [null, 'Immunity'],
      [null, 'Immunization'],
      [null, 'Immunological agents (Immunomodulators)'],
      [null, 'Adverse reactions'],
      [null, 'Antineoplastic Agents (Anticancer Drugs)'],
    ],
    'Psychopharmacology' => [
      [null, 'Anxiolytics'],
      [null, 'Hypnotics'],
      [null, 'Mood stabilizers'],
      [null, 'Anti-depressants'],
      [null, 'Anti-psychotics'],
      [null, 'Anticonvulsants'],
    ],
    'Narcotics' => [
      [null, 'Different types of narcotics'],
      [null, 'Prescription, Dispensing & Storage of narcotics'],
      [null, 'Seminar Question with Dangers of Narcotics'],
      [null, 'Legal implications of Narcotics'],
      [null, 'Narcotic drug abuse & Management'],
    ],
    'Poison and non-medical use of drugs' => [],
  ],

  'DNE 121' => [
    'Introduction to Primary health care' => [
      [null, 'Updated Introduction to Primary Health Care'],
      [null, 'Concepts of Primary Health Care (Principles, Pillars, Elements & Strategies)'],
      [null, 'Planning, Implementation, Monitoring & Evaluation of PHC Activities'],
      [null, 'Concept of the community'],
      [null, 'Concept of Health (Determinants & Dimensions)'],
      [null, 'Health and Disease (Outbreak, Natural History of a disease, Surveillance & Malnutrition)'],
      [null, "Sustainable Development Goals (SDG's)"],
      [null, 'Integrated Disease Surveillance'],
    ],
    'Community Based Health Care (CBHC)' => [
      [null, 'Introduction to community based health care'],
      [null, 'Community Approach'],
      [null, 'Community Entry'],
      [null, 'Community Survey'],
      [null, 'Community Assessment'],
      [null, 'Community situation Analysis (Diagnosis)'],
      [null, 'Community Mobilization'],
      [null, 'Community Participation'],
      [null, 'Community Organization'],
      [null, 'Community Dialogue'],
      [null, 'Community Empowerment'],
      [null, 'School Health Program'],
      [null, 'Home Visiting'],
      [null, 'Community based rehabilitative services for disabled and disadvantaged groups'],
    ],
  ],

  'DNE 122' => [
    'Introduction to nursing research' => [
      [null, 'Introduction to research'],
      [null, 'Terminologies'],
      [null, 'Research Ethics'],
      [null, 'Purpose of studying research'],
      [null, 'Research techniques (Qualitative, quantitative and their approaches)'],
    ],
    'Writing a research proposal and report' => [
      [null, 'Steps & Phases in Research Process'],
      [null, 'Formulation of research Problem, Topics, Objectives'],
      [null, 'Writing a research proposal & Marking Guide'],
      [null, 'Preliminary Pages'],
      [null, 'Chapter One: Introduction & Sections'],
      [null, 'Chapter Two: Literature review'],
      [null, 'Chapter Three: Methodology'],
      [null, 'References/Referencing'],
      [null, 'Appendices & Consent Form'],
      [null, 'Research Proposal Defense'],
      [null, 'Chapter Four: Results'],
      [null, 'Chapter Five: Discussion, Conclusion and Recommendations'],
      [null, 'Research report & Differences'],
    ],
    'Introduction to teaching methodology and Concept of education' => [
      [null, 'Teaching and Learning process'],
      [null, 'Principles of teaching and learning (Characteristics & Maxims)'],
      [null, 'Teaching Learning Methods'],
      [null, 'Communication and Human relations'],
      [null, 'Teaching technology'],
      [null, 'Assessment and Evaluation'],
    ],
    'Philosophy and psychology of education' => [],
    'Andrology' => [],
    'Teaching-learning (educational) objectives' => [],
    'Educational technology and Teaching aids' => [
      [null, 'Teaching aids'],
    ],
    'Planning teaching' => [],
  ],

  'DNE 123' => [
    'Palliative Care concepts' => [
      [null, 'Introduction to Palliative Care'],
      [null, "Importance's, Roles, Attributes and Components of Palliative Care"],
      [null, 'Principles of Palliative Care'],
      [null, 'Models of Palliative Care'],
      [null, 'Communication – Preparation of family to make important decisions'],
    ],
    'The hospice concept' => [
      [null, 'Hospice movement'],
      [null, 'Philosophy of hospice'],
      [null, 'Goals of hospice'],
      [null, 'Holistic care approach'],
    ],
    'Pain' => [
      [null, 'Introduction to Pain'],
      [null, 'Pain Assessment'],
      [null, 'Pain Management'],
      [null, 'Psychosocial support to terminally ill patients'],
    ],
    'Palliative care emergencies' => [
      [null, 'Introduction to Palliative care emergencies & Severe uncontrolled pain'],
      [null, 'Spinal cord compression'],
      [null, 'Hypercalcemia'],
      [null, 'Hemorrhage'],
      [null, 'Superior Vena Cava Obstruction (SVCO)'],
    ],
    'Symptoms of terminally ill patients' => [
      [null, 'Principles'],
      [null, 'GIT (Nausea and vomiting, Diarrhea, Anorexia, Constipation, Hiccups)'],
      [null, 'Respiratory system (Dyspnea, Cough, Breathlessness, Rattle)'],
      [null, 'Nervous system (Delirium, Depression, Insomnia)'],
      [null, 'Skin & Integumentary system (Non-healing wound, Pruritis, Wound Care)'],
      [null, 'Genitourinary system (Incontinence, Retention)'],
    ],
    'Common conditions in palliative care' => [
      [null, 'Anger'],
      [null, 'Spiritual needs and Johari Window'],
      [null, 'Bereavement'],
    ],
    'Ethics at the end of life' => [
      [null, 'Hastened death'],
      [null, 'Assisted death'],
      [null, 'Advanced directives'],
      [null, 'Will Making'],
    ],
    'Terminal care' => [
      [null, 'Nearing death awareness'],
      [null, 'Euthanasia'],
      [null, 'Grief'],
      [null, 'Death and dying'],
      [null, 'Breaking sad news'],
    ],
  ],

  'DNE 124' => [
    'Disaster' => [
      [null, 'Introduction to Disaster in Nursing'],
      [null, 'Natural disaster'],
      [null, 'Man made disaster'],
    ],
    'Disaster management' => [
      [null, 'The Stages of Disaster Management'],
      [null, 'Roles played by each stakeholder in Disaster Management'],
      [null, 'Mass Causality Incident & Triage'],
      [null, 'Community Participation in Disaster Management'],
      [null, 'Requirements for disaster preparedness'],
    ],
    'Disaster prevention' => [
      [null, 'Natural prevention'],
      [null, 'Artificial prevention'],
    ],
    'Introduction to occupational health hazards' => [
      [null, 'Introduction (Aims, Principles, Components, Elements)'],
      [null, 'Identification of occupational health hazards in different work places'],
      [null, 'Types of occupational health hazards'],
      [null, 'Prevention and control of occupational health hazards'],
      [null, 'Occupational Hazard Control'],
      [null, 'Workers compensation act'],
      [null, 'Occupational Health Service Program'],
      [null, "PPE's and Fire Extinguishers"],
      [null, 'Injection Safety and Disposal'],
      [null, 'Waste Management'],
      [null, 'Work related injuries and Fatalities'],
      [null, 'Psychosocial aspects of work: Job stress and associated conditions'],
    ],
  ],

  'DNE 212' => [
    'Manage women with gynecological conditions' => [
      [null, 'Introduction to Gynaecology'],
      [null, 'History, Examinations and Investigations'],
      [null, 'Menstruation & Menstruation Disorders'],
      [null, 'Abortions'],
      [null, 'Ectopic Pregnancy'],
      [null, 'Cervical Erosion, Trauma and Polyps'],
      [null, 'Pelvic Inflammatory Diseases'],
      [null, 'Infertility'],
      [null, 'Vesico-Vaginal Fistula (VVF) and Recto-Vaginal fistula (RVF)'],
      [null, 'Cancers of Reproductive Health Organs (Cervix, Breast, Uterus and Ovaries)'],
      [null, 'Fibroids'],
      [null, 'Congenital abnormalities of the reproductive organs'],
      [null, 'Prolapse of the uterus, cervix and bladder'],
      [null, 'Ovarian cyst'],
    ],
    'Applied anatomy of the female and male reproductive organ' => [],
    'Adolescent reproductive health' => [
      [null, 'Introduction to Reproductive Health'],
      [null, 'Integration of Reproductive Health Services'],
      [null, 'Adolescent Reproductive Health and Development'],
      [null, 'Adolescent friendly health services'],
      [null, 'Adolescent Sexuality'],
      [null, 'Vulnerable groups'],
      [null, 'Community involvement in adolescent reproductive health'],
    ],
    'Family planning' => [],
    'Sexually transmitted infections (STI)' => [
      [null, 'Management of HIV/AIDs'],
      [null, 'Opportunistic Infections and Hepatitis'],
      [null, "Post exposure prophylaxis (PEP and ARV's)"],
      [null, 'PMTCT and Care of Infant'],
    ],
    'Post abortion care' => [],
  ],

  'DNE 213' => [
    'Management' => [
      [null, 'Introduction to Health Service Management'],
      [null, 'Management theories and Styles'],
      [null, 'Principles of Management'],
      [null, 'Levels and Functions of Management'],
      [null, 'Human resource management'],
      [null, 'Human Resource Planning'],
      [null, 'Staff recruitment process'],
      [null, 'Financial management, Budgeting and Accountability'],
      [null, 'Management of equipment and supplies'],
      [null, 'Transport management'],
      [null, 'Management of Infrastructure'],
      [null, 'Integrated disease response and surveillance'],
      [null, 'Key government policies (Uganda Healthcare System)'],
    ],
    'Leadership' => [
      [null, 'Introduction, Kinds, Power and Authority'],
      [null, 'Leadership theories'],
      [null, 'Team process'],
      [null, 'Styles of leadership'],
      [null, 'Staff Delegation'],
      [null, 'Conflict and conflict resolution'],
      [null, 'Negotiation Skills'],
      [null, 'Support Supervision'],
    ],
    'Concept of entrepreneurship' => [
      [null, 'Introduction to Entrepreneurship'],
      [null, 'Entrepreneur as a Manager and Entrepreneurial Process'],
      [null, 'Small business in the economy'],
      [null, 'Entrepreneurship Skills'],
    ],
    'Creating entrepreneurial small business' => [
      [null, 'Business idea and Opportunity'],
      [null, 'Types of Business Enterprises'],
      [null, 'Business or Business Enterprise'],
      [null, 'Business planning'],
      [null, 'Successful strategies for small business'],
      [null, 'Start-ups and franchises (Permits/license)'],
      [null, 'Buying an existing business'],
      [null, 'Forming and protecting a business'],
    ],
    'Managing people and resources' => [
      [null, 'Customer Care'],
      [null, 'Marketing'],
      [null, 'Money matters for small business'],
      [null, 'Business exits and realizing value'],
    ],
  ],

];

// ── Seed ─────────────────────────────────────────────────────────────────────
$findModule = $pdo->prepare('SELECT id FROM curriculum_modules WHERE code = ?');
$findTopic = $pdo->prepare('SELECT id FROM topics WHERE module_id = ? AND title = ?');
$insTopic = $pdo->prepare('INSERT INTO topics (module_id, title, sort_order) VALUES (?, ?, ?)');
$findSubtopic = $pdo->prepare('SELECT id FROM subtopics WHERE topic_id = ? AND title = ?');
$insSubtopic = $pdo->prepare('INSERT INTO subtopics (topic_id, code, title, sort_order) VALUES (?, ?, ?, ?)');

$topicCount = 0;
$subtopicCount = 0;
$skippedModules = [];

foreach ($DATA as $moduleCode => $topics) {
  $findModule->execute([$moduleCode]);
  $moduleId = $findModule->fetchColumn();
  if (!$moduleId) {
    $skippedModules[] = $moduleCode;
    continue;
  }

  $topicOrder = 0;
  foreach ($topics as $topicTitle => $subtopics) {
    $topicOrder++;
    $findTopic->execute([$moduleId, $topicTitle]);
    $topicId = $findTopic->fetchColumn();
    if (!$topicId) {
      $insTopic->execute([$moduleId, $topicTitle, $topicOrder]);
      $topicId = $pdo->lastInsertId();
      $topicCount++;
    }

    $subOrder = 0;
    foreach ($subtopics as [$code, $subtitle]) {
      $subOrder++;
      $findSubtopic->execute([$topicId, $subtitle]);
      if (!$findSubtopic->fetchColumn()) {
        $insSubtopic->execute([$topicId, $code, $subtitle, $subOrder]);
        $subtopicCount++;
      }
    }
  }
}

echo "Seeded $topicCount topics and $subtopicCount subtopics.\n";
if ($skippedModules) {
  echo "WARNING: module code(s) not found, skipped: " . implode(', ', $skippedModules) . "\n";
}
