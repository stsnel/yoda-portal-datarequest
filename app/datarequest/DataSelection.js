import React, { Component } from "react";
import { render } from "react-dom";
import BootstrapTable from 'react-bootstrap-table-next';
import filterFactory, { numberFilter, textFilter, selectFilter, multiSelectFilter, Comparator } from 'react-bootstrap-table2-filter';
import paginationFactory from 'react-bootstrap-table2-paginator';

const data = [
  { expId: 1, expCohort: 1, expWave: 7, expType: 0, expSubject: 0, expName: "Blood", expInfo: "" },
  { expId: 2, expCohort: 1, expWave: 8, expType: 0, expSubject: 0, expName: "Blood", expInfo: "" },
  { expId: 3, expCohort: 1, expWave: 9, expType: 0, expSubject: 0, expName: "Blood", expInfo: "" },
  { expId: 4, expCohort: 1, expWave: 7, expType: 0, expSubject: 0, expName: "Buccal", expInfo: "" },
  { expId: 5, expCohort: 1, expWave: 8, expType: 0, expSubject: 0, expName: "Buccal", expInfo: "" },
  { expId: 6, expCohort: 1, expWave: 9, expType: 0, expSubject: 0, expName: "Buccal", expInfo: "" },
  { expId: 7, expCohort: 1, expWave: 7, expType: 0, expSubject: 0, expName: "Hair", expInfo: "" },
  { expId: 8, expCohort: 1, expWave: 8, expType: 0, expSubject: 0, expName: "Hair", expInfo: "" },
  { expId: 9, expCohort: 1, expWave: 9, expType: 0, expSubject: 0, expName: "Hair", expInfo: "" },
  { expId: 10, expCohort: 1, expWave: 7, expType: 0, expSubject: 0, expName: "Saliva", expInfo: "" },
  { expId: 11, expCohort: 1, expWave: 8, expType: 0, expSubject: 0, expName: "Saliva", expInfo: "" },
  { expId: 12, expCohort: 1, expWave: 9, expType: 0, expSubject: 0, expName: "Saliva", expInfo: "" },
  { expId: 13, expCohort: 1, expWave: 7, expType: 0, expSubject: 1, expName: "Blood", expInfo: "" },
  { expId: 14, expCohort: 1, expWave: 7, expType: 0, expSubject: 1, expName: "Buccal", expInfo: "" },
  { expId: 15, expCohort: 1, expWave: 7, expType: 0, expSubject: 2, expName: "Blood", expInfo: "" },
  { expId: 16, expCohort: 1, expWave: 7, expType: 0, expSubject: 2, expName: "Buccal", expInfo: "" },
  { expId: 17, expCohort: 1, expWave: 7, expType: 1, expSubject: 0, expName: "Length and weight", expInfo: "" },
  { expId: 18, expCohort: 1, expWave: 7, expType: 2, expSubject: 0, expName: "Cyberball", expInfo: "" },
  { expId: 19, expCohort: 1, expWave: 8, expType: 2, expSubject: 0, expName: "Cyberball", expInfo: "" },
  { expId: 20, expCohort: 1, expWave: 9, expType: 2, expSubject: 0, expName: "Cyberball", expInfo: "" },
  { expId: 21, expCohort: 1, expWave: 7, expType: 2, expSubject: 0, expName: "Discount (Delay Gratification)", expInfo: "" },
  { expId: 22, expCohort: 1, expWave: 8, expType: 2, expSubject: 0, expName: "Discount (Delay Gratification)", expInfo: "" },
  { expId: 23, expCohort: 1, expWave: 9, expType: 2, expSubject: 0, expName: "Discount (Delay Gratification)", expInfo: "" },
  { expId: 24, expCohort: 1, expWave: 7, expType: 2, expSubject: 0, expName: "Peabody", expInfo: "" },
  { expId: 25, expCohort: 1, expWave: 8, expType: 2, expSubject: 0, expName: "Peabody", expInfo: "" },
  { expId: 26, expCohort: 1, expWave: 9, expType: 2, expSubject: 0, expName: "Peabody", expInfo: "" },
  { expId: 27, expCohort: 1, expWave: 7, expType: 2, expSubject: 0, expName: "Penn emotion recognition test", expInfo: "" },
  { expId: 28, expCohort: 1, expWave: 8, expType: 2, expSubject: 0, expName: "Penn emotion recognition test", expInfo: "" },
  { expId: 29, expCohort: 1, expWave: 9, expType: 2, expSubject: 0, expName: "Penn emotion recognition test", expInfo: "" },
  { expId: 30, expCohort: 1, expWave: 7, expType: 2, expSubject: 0, expName: "Penn motor praxis test", expInfo: "" },
  { expId: 31, expCohort: 1, expWave: 8, expType: 2, expSubject: 0, expName: "Penn motor praxis test", expInfo: "" },
  { expId: 32, expCohort: 1, expWave: 9, expType: 2, expSubject: 0, expName: "Penn motor praxis test", expInfo: "" },
  { expId: 33, expCohort: 1, expWave: 7, expType: 2, expSubject: 0, expName: "Penn word memory test delay", expInfo: "" },
  { expId: 34, expCohort: 1, expWave: 8, expType: 2, expSubject: 0, expName: "Penn word memory test delay", expInfo: "" },
  { expId: 35, expCohort: 1, expWave: 9, expType: 2, expSubject: 0, expName: "Penn word memory test delay", expInfo: "" },
  { expId: 36, expCohort: 1, expWave: 7, expType: 2, expSubject: 0, expName: "Penn word memory test", expInfo: "" },
  { expId: 37, expCohort: 1, expWave: 8, expType: 2, expSubject: 0, expName: "Penn word memory test", expInfo: "" },
  { expId: 38, expCohort: 1, expWave: 9, expType: 2, expSubject: 0, expName: "Penn word memory test", expInfo: "" },
  { expId: 39, expCohort: 1, expWave: 7, expType: 2, expSubject: 0, expName: "Trust game", expInfo: "" },
  { expId: 40, expCohort: 1, expWave: 8, expType: 2, expSubject: 0, expName: "Trust game", expInfo: "" },
  { expId: 41, expCohort: 1, expWave: 9, expType: 2, expSubject: 0, expName: "Trust game", expInfo: "" },
  { expId: 42, expCohort: 1, expWave: 7, expType: 5, expSubject: 0, expName: "Child Gap antisaccade", expInfo: "" },
  { expId: 43, expCohort: 1, expWave: 8, expType: 5, expSubject: 0, expName: "Child Gap antisaccade", expInfo: "" },
  { expId: 44, expCohort: 1, expWave: 9, expType: 5, expSubject: 0, expName: "Child Gap antisaccade", expInfo: "" },
  { expId: 45, expCohort: 1, expWave: 7, expType: 5, expSubject: 0, expName: "Dual Eyetracking", expInfo: "" },
  { expId: 46, expCohort: 1, expWave: 7, expType: 5, expSubject: 0, expName: "Child Gap prosaccade", expInfo: "" },
  { expId: 47, expCohort: 1, expWave: 8, expType: 5, expSubject: 0, expName: "Child Gap prosaccade", expInfo: "" },
  { expId: 48, expCohort: 1, expWave: 9, expType: 5, expSubject: 0, expName: "Child Gap prosaccade", expInfo: "" },
  { expId: 49, expCohort: 1, expWave: 7, expType: 5, expSubject: 0, expName: "Child Social Gaze", expInfo: "" },
  { expId: 50, expCohort: 1, expWave: 8, expType: 5, expSubject: 0, expName: "Child Social Gaze", expInfo: "" },
  { expId: 51, expCohort: 1, expWave: 9, expType: 5, expSubject: 0, expName: "Child Social Gaze", expInfo: "" },
  { expId: 52, expCohort: 1, expWave: 7, expType: 6, expSubject: 0, expName: "WISC-III", expInfo: "" },
  { expId: 53, expCohort: 1, expWave: 8, expType: 6, expSubject: 0, expName: "WISC-III", expInfo: "" },
  { expId: 54, expCohort: 1, expWave: 9, expType: 6, expSubject: 0, expName: "WISC-III", expInfo: "" },
  { expId: 55, expCohort: 1, expWave: 7, expType: 6, expSubject: 0, expName: "WISC-V", expInfo: "" },
  { expId: 56, expCohort: 1, expWave: 8, expType: 6, expSubject: 0, expName: "WISC-V", expInfo: "" },
  { expId: 57, expCohort: 1, expWave: 7, expType: 7, expSubject: 0, expName: "Inhibition experiment", expInfo: "" },
  { expId: 58, expCohort: 1, expWave: 8, expType: 7, expSubject: 0, expName: "Inhibition experiment", expInfo: "" },
  { expId: 59, expCohort: 1, expWave: 9, expType: 7, expSubject: 0, expName: "Inhibition experiment", expInfo: "" },
  { expId: 60, expCohort: 1, expWave: 7, expType: 8, expSubject: 0, expName: "Anatomy experiment", expInfo: "" },
  { expId: 61, expCohort: 1, expWave: 8, expType: 8, expSubject: 0, expName: "Anatomy experiment", expInfo: "" },
  { expId: 62, expCohort: 1, expWave: 9, expType: 8, expSubject: 0, expName: "Anatomy experiment", expInfo: "" },
  { expId: 63, expCohort: 1, expWave: 7, expType: 8, expSubject: 0, expName: "Dti experiment", expInfo: "Diffusion Tensor Imaging (DTI) - Fiber Tracking" },
  { expId: 64, expCohort: 1, expWave: 8, expType: 8, expSubject: 0, expName: "Dti experiment", expInfo: "Diffusion Tensor Imaging (DTI) - Fiber Tracking" },
  { expId: 65, expCohort: 1, expWave: 9, expType: 8, expSubject: 0, expName: "Dti experiment", expInfo: "Diffusion Tensor Imaging (DTI) - Fiber Tracking" },
  { expId: 66, expCohort: 1, expWave: 7, expType: 8, expSubject: 0, expName: "Functional MRI Emotion experiment", expInfo: "" },
  { expId: 67, expCohort: 1, expWave: 8, expType: 8, expSubject: 0, expName: "Functional MRI Emotion experiment", expInfo: "" },
  { expId: 68, expCohort: 1, expWave: 9, expType: 8, expSubject: 0, expName: "Functional MRI Emotion experiment", expInfo: "" },
  { expId: 69, expCohort: 1, expWave: 7, expType: 8, expSubject: 0, expName: "Functional MRI Inhibition experiment", expInfo: "" },
  { expId: 70, expCohort: 1, expWave: 8, expType: 8, expSubject: 0, expName: "Functional MRI Inhibition experiment", expInfo: "" },
  { expId: 71, expCohort: 1, expWave: 9, expType: 8, expSubject: 0, expName: "Functional MRI Inhibition experiment", expInfo: "" },
  { expId: 72, expCohort: 1, expWave: 7, expType: 8, expSubject: 0, expName: "Resting state experiment", expInfo: "" },
  { expId: 73, expCohort: 1, expWave: 8, expType: 8, expSubject: 0, expName: "Resting state experiment", expInfo: "" },
  { expId: 74, expCohort: 1, expWave: 9, expType: 8, expSubject: 0, expName: "Resting state experiment", expInfo: "" },
  { expId: 75, expCohort: 1, expWave: 7, expType: 9, expSubject: 0, expName: "Parent Child Interaction", expInfo: "Parent child interaction (PCI) is recorded to allow researchers to code qualitative aspects of the observed interaction between parent and child based on explicitly defined behaviors. The PCI consists of age appropriate structured tasks that include a common mildly stressful event (discussing a difficult topic), and a pleasant event (discussing a pleasant topic). The PCI tasks take about 15 minutes to complete." },
  { expId: 76, expCohort: 1, expWave: 8, expType: 9, expSubject: 0, expName: "Parent Child Interaction", expInfo: "Parent child interaction (PCI) is recorded to allow researchers to code qualitative aspects of the observed interaction between parent and child based on explicitly defined behaviors. The PCI consists of age appropriate structured tasks that include a common mildly stressful event (discussing a difficult topic), and a pleasant event (discussing a pleasant topic). The PCI tasks take about 15 minutes to complete." },
  { expId: 77, expCohort: 1, expWave: 9, expType: 9, expSubject: 0, expName: "Parent Child Interaction", expInfo: "Parent child interaction (PCI) is recorded to allow researchers to code qualitative aspects of the observed interaction between parent and child based on explicitly defined behaviors. The PCI consists of age appropriate structured tasks that include a common mildly stressful event (discussing a difficult topic), and a pleasant event (discussing a pleasant topic). The PCI tasks take about 15 minutes to complete." },
  { expId: 78, expCohort: 1, expWave: 7, expType: 10, expSubject: 0, expName: "Child Behavior Questionnaire", expInfo: "Behavior Questionnaire (EATQ-R)" },
  { expId: 79, expCohort: 1, expWave: 8, expType: 10, expSubject: 0, expName: "Child Behavior Questionnaire", expInfo: "Behavior Questionnaire (EATQ-R)" },
  { expId: 80, expCohort: 1, expWave: 7, expType: 10, expSubject: 0, expName: "Child's sense of compentence", expInfo: "Competentie belevingsschaal - adolescent (CBSA)" },
  { expId: 81, expCohort: 1, expWave: 8, expType: 10, expSubject: 0, expName: "Child's sense of compentence", expInfo: "Competentie belevingsschaal - adolescent (CBSA)" },
  { expId: 82, expCohort: 1, expWave: 7, expType: 10, expSubject: 0, expName: "Child's report of parental behavior inventory", expInfo: "Child's report of parental behavior inventory (CRPBI)" },
  { expId: 83, expCohort: 1, expWave: 8, expType: 10, expSubject: 0, expName: "Child's report of parental behavior inventory", expInfo: "Child's report of parental behavior inventory (CRPBI)" },
  { expId: 84, expCohort: 1, expWave: 8, expType: 10, expSubject: 0, expName: "Eating behavior", expInfo: "Eating behavior - developed by Juliëtte van der Wal, Gerdien Dalmeijer (Whistler) and Charlotte Onland-Moret" },
  { expId: 85, expCohort: 1, expWave: 7, expType: 10, expSubject: 0, expName: "Movies and series", expInfo: "Fictievragenlijst deel 2 kijkgedrag (FVL): Fiction questionnaire - part 2 movies and series" },
  { expId: 86, expCohort: 1, expWave: 8, expType: 10, expSubject: 0, expName: "Movies and series", expInfo: "Fictievragenlijst deel 2 kijkgedrag (FVL): Fiction questionnaire - part 2 movies and series" },
  { expId: 87, expCohort: 1, expWave: 7, expType: 10, expSubject: 0, expName: "Reading behavior", expInfo: "Fictievragenlijst deel 1 leesgedrag (FVL): Fiction questionnaire - part 1 reading behavior" },
  { expId: 88, expCohort: 1, expWave: 8, expType: 10, expSubject: 0, expName: "Reading behavior", expInfo: "Fictievragenlijst deel 1 leesgedrag (FVL): Fiction questionnaire - part 1 reading behavior" },
  { expId: 89, expCohort: 1, expWave: 7, expType: 10, expSubject: 0, expName: "Interpersonal Reactivity Index", expInfo: "Interpersonal Reactivity Index (IRI)" },
  { expId: 90, expCohort: 1, expWave: 8, expType: 10, expSubject: 0, expName: "Interpersonal Reactivity Index", expInfo: "Interpersonal Reactivity Index (IRI)" },
  { expId: 91, expCohort: 1, expWave: 7, expType: 10, expSubject: 0, expName: "Media use", expInfo: "Use of (computer) games and social media" },
  { expId: 92, expCohort: 1, expWave: 8, expType: 10, expSubject: 0, expName: "Media use", expInfo: "Use of (computer) games and social media" },
  { expId: 93, expCohort: 1, expWave: 7, expType: 10, expSubject: 0, expName: "Network Relationships Inventory - Short Form", expInfo: "Friendship: Network Relationships Inventory - Short Form (NRI-SF)" },
  { expId: 94, expCohort: 1, expWave: 8, expType: 10, expSubject: 0, expName: "Network Relationships Inventory - Short Form", expInfo: "Friendship: Network Relationships Inventory - Short Form (NRI-SF)" },
  { expId: 95, expCohort: 1, expWave: 8, expType: 10, expSubject: 0, expName: "Parenting Practices", expInfo: "Parenting Practices (PP)" },
  { expId: 96, expCohort: 1, expWave: 7, expType: 10, expSubject: 0, expName: "Physical Activity Questionnaire", expInfo: "Physical Activity Questionnaire (PAQ-C, PAQ-A)" },
  { expId: 97, expCohort: 1, expWave: 8, expType: 10, expSubject: 0, expName: "Physical Activity Questionnaire", expInfo: "Physical Activity Questionnaire (PAQ-C, PAQ-A)" },
  { expId: 98, expCohort: 1, expWave: 7, expType: 10, expSubject: 0, expName: "Parental Control Scale ", expInfo: "Parental control scale (PCS)" },
  { expId: 99, expCohort: 1, expWave: 8, expType: 10, expSubject: 0, expName: "Parental Control Scale ", expInfo: "Parental control scale (PCS)" },
  { expId: 100, expCohort: 1, expWave: 7, expType: 10, expSubject: 0, expName: "Pubertal development", expInfo: "Pubertal development scale (PDS)" },
  { expId: 101, expCohort: 1, expWave: 8, expType: 10, expSubject: 0, expName: "Pubertal development", expInfo: "Pubertal development scale (PDS)" },
  { expId: 102, expCohort: 1, expWave: 8, expType: 10, expSubject: 0, expName: "Sleep behavior", expInfo: "PROMIS® Pediatric Item Bank v1.0 - Sleep practices, Sleep disturbance, Sleep related impairment" },
  { expId: 103, expCohort: 1, expWave: 7, expType: 10, expSubject: 0, expName: "Impulsivity and risk behavior", expInfo: "Behavioral inhibition scale (BIS) and risk behavior (substance (ab)use)" },
  { expId: 104, expCohort: 1, expWave: 8, expType: 10, expSubject: 0, expName: "Impulsivity and risk behavior", expInfo: "Behavioral inhibition scale (BIS) and risk behavior (substance (ab)use)" },
  { expId: 105, expCohort: 1, expWave: 8, expType: 10, expSubject: 0, expName: "Strengths and difficulties questionnaire", expInfo: "Strengths and difficulties questionnaire (SDQ)" },
  { expId: 106, expCohort: 1, expWave: 8, expType: 10, expSubject: 0, expName: "Sexual development", expInfo: "Love, relationships and (online) sexual behavior" },
  { expId: 107, expCohort: 1, expWave: 7, expType: 10, expSubject: 0, expName: "Sleep behavior", expInfo: "Sleep self report (SSR)" },
  { expId: 108, expCohort: 1, expWave: 7, expType: 10, expSubject: 1, expName: "Adult Self Report", expInfo: "Adult Self Report (ASR)" },
  { expId: 109, expCohort: 1, expWave: 8, expType: 10, expSubject: 1, expName: "Adult Self Report", expInfo: "Adult Self Report (ASR)" },
  { expId: 110, expCohort: 1, expWave: 7, expType: 10, expSubject: 1, expName: "Childhood trauma questionnaire", expInfo: "Childhood Trauma Questionnaire (CTQ)" },
  { expId: 111, expCohort: 1, expWave: 7, expType: 10, expSubject: 1, expName: "Demographics", expInfo: "Household, background, language, education, family relations, economic situation, religion (or updates in wave Rondom 0)" },
  { expId: 112, expCohort: 1, expWave: 8, expType: 10, expSubject: 1, expName: "Demographics", expInfo: "Household, background, language, education, family relations, economic situation, religion (or updates in wave Rondom 0)" },
  { expId: 113, expCohort: 1, expWave: 7, expType: 10, expSubject: 1, expName: "Family illness - medical", expInfo: "Medical problems of first degree family members" },
  { expId: 114, expCohort: 1, expWave: 7, expType: 10, expSubject: 1, expName: "Family illness - psychiatric", expInfo: "Psychiatric problems of first degree family members" },
  { expId: 115, expCohort: 1, expWave: 8, expType: 10, expSubject: 1, expName: "Family illness - psychiatric", expInfo: "Psychiatric problems of first degree family members" },
  { expId: 116, expCohort: 1, expWave: 7, expType: 10, expSubject: 1, expName: "General health", expInfo: "General health questionnaire" },
  { expId: 117, expCohort: 1, expWave: 8, expType: 10, expSubject: 1, expName: "General health", expInfo: "General health questionnaire" },
  { expId: 118, expCohort: 1, expWave: 7, expType: 10, expSubject: 1, expName: "Major life events", expInfo: "Major life events in the past 12 months" },
  { expId: 119, expCohort: 1, expWave: 8, expType: 10, expSubject: 1, expName: "Major life events", expInfo: "Major life events in the past 12 months" },
  { expId: 120, expCohort: 1, expWave: 7, expType: 10, expSubject: 1, expName: "Lifestyle prior to pregnancy", expInfo: "Vitamins, medication, exposure during pregnancy" },
  { expId: 121, expCohort: 1, expWave: 7, expType: 10, expSubject: 1, expName: "Lifestyle", expInfo: "Substance (ab)use" },
  { expId: 122, expCohort: 1, expWave: 8, expType: 10, expSubject: 1, expName: "Lifestyle", expInfo: "Substance (ab)use" },
  { expId: 123, expCohort: 1, expWave: 7, expType: 10, expSubject: 1, expName: "List of longterm stressful life events", expInfo: "List of longterm stressful life events selected by GenerationR" },
  { expId: 124, expCohort: 1, expWave: 8, expType: 10, expSubject: 1, expName: "List of longterm stressful life events", expInfo: "List of longterm stressful life events selected by GenerationR" },
  { expId: 125, expCohort: 1, expWave: 7, expType: 10, expSubject: 1, expName: "Personality: NEO-FFI-3", expInfo: "Personality questionnaire (NEO-FFI-3)" },
  { expId: 126, expCohort: 1, expWave: 7, expType: 10, expSubject: 1, expName: "Portrait values questionnaire - revised", expInfo: "Portrait values questionnaire - revised (PVQ-RR)" },
  { expId: 127, expCohort: 1, expWave: 7, expType: 10, expSubject: 1, expName: "Social Responsiveness Scale for Adults", expInfo: "Social Responsiveness Scale for Adults (SRS-A)" },
  { expId: 128, expCohort: 1, expWave: 7, expType: 10, expSubject: 1, expName: "Coping with situations", expInfo: "Utrechtse Coping Lijst (UCL)" },
  { expId: 129, expCohort: 1, expWave: 8, expType: 10, expSubject: 1, expName: "Coping with situations", expInfo: "Utrechtse Coping Lijst (UCL)" },
  { expId: 130, expCohort: 1, expWave: 7, expType: 10, expSubject: 2, expName: "Adult Self Report", expInfo: "Adult Self Report (ASR)" },
  { expId: 131, expCohort: 1, expWave: 8, expType: 10, expSubject: 2, expName: "Adult Self Report", expInfo: "Adult Self Report (ASR)" },
  { expId: 132, expCohort: 1, expWave: 7, expType: 10, expSubject: 2, expName: "Periconceptual health", expInfo: "Periconceptual health" },
  { expId: 133, expCohort: 1, expWave: 7, expType: 10, expSubject: 2, expName: "Childhood trauma questionnaire", expInfo: "Childhood Trauma Questionnaire (CTQ)" },
  { expId: 134, expCohort: 1, expWave: 7, expType: 10, expSubject: 2, expName: "Demographics", expInfo: "Household, background, language, education, family relations, economic situation, religion (or updates in wave Rondom 0)" },
  { expId: 135, expCohort: 1, expWave: 8, expType: 10, expSubject: 2, expName: "Demographics", expInfo: "Household, background, language, education, family relations, economic situation, religion (or updates in wave Rondom 0)" },
  { expId: 136, expCohort: 1, expWave: 7, expType: 10, expSubject: 2, expName: "Family illness - medical", expInfo: "Medical problems of first degree family members" },
  { expId: 137, expCohort: 1, expWave: 7, expType: 10, expSubject: 2, expName: "Family illness - psychiatric", expInfo: "Psychiatric problems of first degree family members" },
  { expId: 138, expCohort: 1, expWave: 8, expType: 10, expSubject: 2, expName: "Family illness - psychiatric", expInfo: "Psychiatric problems of first degree family members" },
  { expId: 139, expCohort: 1, expWave: 7, expType: 10, expSubject: 2, expName: "General health", expInfo: "General health questionnaire" },
  { expId: 140, expCohort: 1, expWave: 8, expType: 10, expSubject: 2, expName: "General health", expInfo: "General health questionnaire" },
  { expId: 141, expCohort: 1, expWave: 7, expType: 10, expSubject: 2, expName: "Major life events", expInfo: "Major life events in the past 12 months" },
  { expId: 142, expCohort: 1, expWave: 8, expType: 10, expSubject: 2, expName: "Major life events", expInfo: "Major life events in the past 12 months" },
  { expId: 143, expCohort: 1, expWave: 7, expType: 10, expSubject: 2, expName: "Lifestyle during pregnancy", expInfo: "Vitamins, medication, exposure during pregnancy" },
  { expId: 144, expCohort: 1, expWave: 7, expType: 10, expSubject: 2, expName: "Lifestyle", expInfo: "Substance (ab)use" },
  { expId: 145, expCohort: 1, expWave: 8, expType: 10, expSubject: 2, expName: "Lifestyle", expInfo: "Substance (ab)use" },
  { expId: 146, expCohort: 1, expWave: 7, expType: 10, expSubject: 2, expName: "List of longterm stressful life events", expInfo: "List of longterm stressful life events selected by GenerationR" },
  { expId: 147, expCohort: 1, expWave: 8, expType: 10, expSubject: 2, expName: "List of longterm stressful life events", expInfo: "List of longterm stressful life events selected by GenerationR" },
  { expId: 148, expCohort: 1, expWave: 7, expType: 10, expSubject: 2, expName: "Personality: NEO-FFI-3", expInfo: "Personality questionnaire (NEO-FFI-3)" },
  { expId: 149, expCohort: 1, expWave: 7, expType: 10, expSubject: 2, expName: "Portrait values questionnaire - revised", expInfo: "Portrait values questionnaire - revised (PVQ-RR)" },
  { expId: 150, expCohort: 1, expWave: 7, expType: 10, expSubject: 2, expName: "Social Responsiveness Scale for Adults", expInfo: "Social Responsiveness Scale for Adults (SRS-A)" },
  { expId: 151, expCohort: 1, expWave: 7, expType: 10, expSubject: 2, expName: "Coping with situations", expInfo: "Utrechtse Coping Lijst (UCL)" },
  { expId: 152, expCohort: 1, expWave: 8, expType: 10, expSubject: 2, expName: "Coping with situations", expInfo: "Utrechtse Coping Lijst (UCL)" },
  { expId: 153, expCohort: 1, expWave: 7, expType: 10, expSubject: 3, expName: "ADHD symptoms and gender identity", expInfo: "ADHD symptoms (SWAN rating scale) and Gender Identity (GI)" },
  { expId: 154, expCohort: 1, expWave: 7, expType: 10, expSubject: 3, expName: "Bullying", expInfo: "Bullying behavior of/towards the child and Gender Identity (GI)" },
  { expId: 155, expCohort: 1, expWave: 8, expType: 10, expSubject: 3, expName: "Bullying", expInfo: "Bullying behavior of/towards the child and Gender Identity (GI)" },
  { expId: 156, expCohort: 1, expWave: 7, expType: 10, expSubject: 3, expName: "Child Behavior Checklist", expInfo: "Child Behavior Checklist (CBCL). Questionnaire about problem behavior and skills of the child" },
  { expId: 157, expCohort: 1, expWave: 8, expType: 10, expSubject: 3, expName: "Child Behavior Checklist", expInfo: "Child Behavior Checklist (CBCL). Questionnaire about problem behavior and skills of the child" },
  { expId: 158, expCohort: 1, expWave: 7, expType: 10, expSubject: 3, expName: "Child Behavior Questionnaire", expInfo: "Behavior Questionnaire (EATQ-R)" },
  { expId: 159, expCohort: 1, expWave: 8, expType: 10, expSubject: 3, expName: "Child Behavior Questionnaire", expInfo: "Behavior Questionnaire (EATQ-R)" },
  { expId: 160, expCohort: 1, expWave: 7, expType: 10, expSubject: 3, expName: "Language situation and pragmatics", expInfo: "Spoken language in child's environment and Clinical Evaluation of Language Fundamentals 4th Edition - subscale Pragmatics (CELF-4 pragmatics)" },
  { expId: 161, expCohort: 1, expWave: 8, expType: 10, expSubject: 3, expName: "Language situation and pragmatics", expInfo: "Spoken language in child's environment and Clinical Evaluation of Language Fundamentals 4th Edition - subscale Pragmatics (CELF-4 pragmatics)" },
  { expId: 162, expCohort: 1, expWave: 7, expType: 10, expSubject: 3, expName: "Eating behavior", expInfo: "RIVM Questionnaire 'Wat eet Nederland?'" },
  { expId: 163, expCohort: 1, expWave: 7, expType: 10, expSubject: 3, expName: "Child Health", expInfo: "Medical questionnaire on child's health" },
  { expId: 164, expCohort: 1, expWave: 8, expType: 10, expSubject: 3, expName: "Child Health", expInfo: "Medical questionnaire on child's health" },
  { expId: 165, expCohort: 1, expWave: 7, expType: 10, expSubject: 3, expName: "Media education", expInfo: "Media education" },
  { expId: 166, expCohort: 1, expWave: 8, expType: 10, expSubject: 3, expName: "Media education", expInfo: "Media education" },
  { expId: 167, expCohort: 1, expWave: 7, expType: 10, expSubject: 3, expName: "Network Relationships Inventory - Short Form - Parent report", expInfo: "Parent-child relation: Network Relationships Inventory - Short Form - Parent report (NRI-SF parent report)" },
  { expId: 168, expCohort: 1, expWave: 8, expType: 10, expSubject: 3, expName: "Network Relationships Inventory - Short Form - Parent report", expInfo: "Parent-child relation: Network Relationships Inventory - Short Form - Parent report (NRI-SF parent report)" },
  { expId: 169, expCohort: 1, expWave: 7, expType: 10, expSubject: 3, expName: "Parenting behavior", expInfo: "Alabamma Parenting Questionnaire (APQ), Nijmeegse Opvoedvragenlijst (NOV), Parenting Dimensions Inventory (PDI)" },
  { expId: 170, expCohort: 1, expWave: 8, expType: 10, expSubject: 3, expName: "Parenting behavior", expInfo: "Alabamma Parenting Questionnaire (APQ), Nijmeegse Opvoedvragenlijst (NOV), Parenting Dimensions Inventory (PDI)" },
  { expId: 171, expCohort: 1, expWave: 7, expType: 10, expSubject: 3, expName: "Parental Stress Index - Acceptance", expInfo: "Nijmeegse Ouderlijke Stress Index (NOSI)/Parental Stress Index (PSI) - subscale Acceptance" },
  { expId: 172, expCohort: 1, expWave: 7, expType: 10, expSubject: 3, expName: "Parental Stress Index - Sense of Competence", expInfo: "Nijmeegse Ouderlijke Stress Index (NOSI)/Parental Stress Index (PSI) - subscale Sense of competence" },
  { expId: 173, expCohort: 1, expWave: 8, expType: 10, expSubject: 3, expName: "Parental Stress Index - Sense of Competence", expInfo: "Nijmeegse Ouderlijke Stress Index (NOSI)/Parental Stress Index (PSI) - subscale Sense of competence" },
  { expId: 174, expCohort: 1, expWave: 7, expType: 10, expSubject: 3, expName: "Characteristics of the child", expInfo: "Quick Big Five (QBF)" },
  { expId: 175, expCohort: 1, expWave: 8, expType: 10, expSubject: 3, expName: "Characteristics of the child", expInfo: "Quick Big Five (QBF)" },
  { expId: 176, expCohort: 1, expWave: 7, expType: 10, expSubject: 3, expName: "Strengths and difficulties questionnaire", expInfo: "Strengths and difficulties questionnaire (SDQ)" },
  { expId: 177, expCohort: 1, expWave: 8, expType: 10, expSubject: 3, expName: "Strengths and difficulties questionnaire", expInfo: "Strengths and difficulties questionnaire (SDQ)" },
  { expId: 178, expCohort: 1, expWave: 8, expType: 10, expSubject: 3, expName: "Parental monitoring questionnaire", expInfo: "Vragenlijst toezicht houden (VTH)/ Parental monitoring questionnaire" },
  { expId: 179, expCohort: 1, expWave: 7, expType: 10, expSubject: 5, expName: "Teacher report form", expInfo: "Teacher Report Form (TRF). Questionnaire about problem behavior and skills of the child" },
  { expId: 180, expCohort: 0, expWave: 3, expType: 0, expSubject: 0, expName: "Buccal", expInfo: "" },
  { expId: 181, expCohort: 0, expWave: 4, expType: 0, expSubject: 0, expName: "Buccal", expInfo: "" },
  { expId: 182, expCohort: 0, expWave: 5, expType: 0, expSubject: 0, expName: "Buccal", expInfo: "" },
  { expId: 183, expCohort: 0, expWave: 6, expType: 0, expSubject: 0, expName: "Buccal", expInfo: "" },
  { expId: 184, expCohort: 0, expWave: 2, expType: 0, expSubject: 0, expName: "Cordblood", expInfo: "" },
  { expId: 185, expCohort: 0, expWave: 4, expType: 0, expSubject: 0, expName: "Hair", expInfo: "" },
  { expId: 186, expCohort: 0, expWave: 5, expType: 0, expSubject: 0, expName: "Hair", expInfo: "" },
  { expId: 187, expCohort: 0, expWave: 0, expType: 0, expSubject: 1, expName: "Blood", expInfo: "" },
  { expId: 188, expCohort: 0, expWave: 0, expType: 0, expSubject: 1, expName: "Buccal", expInfo: "" },
  { expId: 189, expCohort: 0, expWave: 0, expType: 0, expSubject: 2, expName: "Blood", expInfo: "" },
  { expId: 190, expCohort: 0, expWave: 0, expType: 0, expSubject: 2, expName: "Buccal", expInfo: "" },
  { expId: 191, expCohort: 0, expWave: 1, expType: 0, expSubject: 2, expName: "Hair", expInfo: "" },
  { expId: 192, expCohort: 0, expWave: 5, expType: 2, expSubject: 0, expName: "Peabody", expInfo: "" },
  { expId: 193, expCohort: 0, expWave: 0, expType: 3, expSubject: 0, expName: "Echo", expInfo: "" },
  { expId: 194, expCohort: 0, expWave: 1, expType: 3, expSubject: 0, expName: "Echo", expInfo: "" },
  { expId: 195, expCohort: 0, expWave: 3, expType: 4, expSubject: 0, expName: "Coherence", expInfo: "" },
  { expId: 196, expCohort: 0, expWave: 4, expType: 4, expSubject: 0, expName: "Coherence", expInfo: "" },
  { expId: 197, expCohort: 0, expWave: 5, expType: 4, expSubject: 0, expName: "Coherence", expInfo: "" },
  { expId: 198, expCohort: 0, expWave: 6, expType: 4, expSubject: 0, expName: "Coherence", expInfo: "" },
  { expId: 199, expCohort: 0, expWave: 4, expType: 4, expSubject: 0, expName: "Face emotion", expInfo: "" },
  { expId: 200, expCohort: 0, expWave: 5, expType: 4, expSubject: 0, expName: "Face emotion", expInfo: "" },
  { expId: 201, expCohort: 0, expWave: 6, expType: 4, expSubject: 0, expName: "Face emotion", expInfo: "" },
  { expId: 202, expCohort: 0, expWave: 3, expType: 4, expSubject: 0, expName: "Face house", expInfo: "" },
  { expId: 203, expCohort: 0, expWave: 4, expType: 4, expSubject: 0, expName: "Face house", expInfo: "" },
  { expId: 204, expCohort: 0, expWave: 5, expType: 4, expSubject: 0, expName: "Face house", expInfo: "" },
  { expId: 205, expCohort: 0, expWave: 6, expType: 4, expSubject: 0, expName: "Face house", expInfo: "" },
  { expId: 206, expCohort: 0, expWave: 5, expType: 5, expSubject: 0, expName: "Looking While Listening", expInfo: "" },
  { expId: 207, expCohort: 0, expWave: 3, expType: 5, expSubject: 0, expName: "Infant Face Popout", expInfo: "" },
  { expId: 208, expCohort: 0, expWave: 4, expType: 5, expSubject: 0, expName: "Infant Face Popout", expInfo: "" },
  { expId: 209, expCohort: 0, expWave: 5, expType: 5, expSubject: 0, expName: "Infant Face Popout", expInfo: "" },
  { expId: 210, expCohort: 0, expWave: 6, expType: 5, expSubject: 0, expName: "Infant Face Popout", expInfo: "" },
  { expId: 211, expCohort: 0, expWave: 3, expType: 5, expSubject: 0, expName: "Infant Pro Gap", expInfo: "" },
  { expId: 212, expCohort: 0, expWave: 4, expType: 5, expSubject: 0, expName: "Infant Pro Gap", expInfo: "" },
  { expId: 213, expCohort: 0, expWave: 5, expType: 5, expSubject: 0, expName: "Infant Pro Gap", expInfo: "" },
  { expId: 214, expCohort: 0, expWave: 6, expType: 5, expSubject: 0, expName: "Infant Pro Gap", expInfo: "" },
  { expId: 215, expCohort: 0, expWave: 3, expType: 5, expSubject: 0, expName: "Infant Social Gaze", expInfo: "" },
  { expId: 216, expCohort: 0, expWave: 4, expType: 5, expSubject: 0, expName: "Infant Social Gaze", expInfo: "" },
  { expId: 217, expCohort: 0, expWave: 5, expType: 5, expSubject: 0, expName: "Infant Social Gaze", expInfo: "" },
  { expId: 218, expCohort: 0, expWave: 6, expType: 5, expSubject: 0, expName: "Infant Social Gaze", expInfo: "" },
  { expId: 219, expCohort: 0, expWave: 3, expType: 9, expSubject: 0, expName: "Parent Child Interaction", expInfo: "Parent child interaction (PCI) is recorded to allow researchers to code qualitative aspects of the observed interaction between parent and child based on explicitly defined behaviors. The PCI consists of age appropriate structured tasks that include a common mildly stressful event (clean-up and a teaching task), and a pleasant event (unstructured free play). The PCI tasks take about 15 minutes to complete." },
  { expId: 220, expCohort: 0, expWave: 4, expType: 9, expSubject: 0, expName: "Parent Child Interaction", expInfo: "Parent child interaction (PCI) is recorded to allow researchers to code qualitative aspects of the observed interaction between parent and child based on explicitly defined behaviors. The PCI consists of age appropriate structured tasks that include a common mildly stressful event (clean-up and a teaching task), and a pleasant event (unstructured free play). The PCI tasks take about 15 minutes to complete." },
  { expId: 221, expCohort: 0, expWave: 5, expType: 9, expSubject: 0, expName: "Parent Child Interaction", expInfo: "Parent child interaction (PCI) is recorded to allow researchers to code qualitative aspects of the observed interaction between parent and child based on explicitly defined behaviors. The PCI consists of age appropriate structured tasks that include a common mildly stressful event (clean-up and a teaching task), and a pleasant event (unstructured free play). The PCI tasks take about 15 minutes to complete." },
  { expId: 222, expCohort: 0, expWave: 6, expType: 9, expSubject: 0, expName: "Parent Child Interaction", expInfo: "Parent child interaction (PCI) is recorded to allow researchers to code qualitative aspects of the observed interaction between parent and child based on explicitly defined behaviors. The PCI consists of age appropriate structured tasks that include a common mildly stressful event (clean-up and a teaching task), and a pleasant event (unstructured free play). The PCI tasks take about 15 minutes to complete." },
  { expId: 223, expCohort: 0, expWave: 0, expType: 10, expSubject: 1, expName: "Adult Self Report", expInfo: "Adult Self Report (ASR)" },
  { expId: 224, expCohort: 0, expWave: 5, expType: 10, expSubject: 1, expName: "Adult Self Report", expInfo: "Adult Self Report (ASR)" },
  { expId: 225, expCohort: 0, expWave: 0, expType: 10, expSubject: 1, expName: "Brief Symptom Inventory", expInfo: "Brief Symptom Inventory (BSI)" },
  { expId: 226, expCohort: 0, expWave: 1, expType: 10, expSubject: 1, expName: "Childhood trauma questionnaire", expInfo: "Childhood Trauma Questionnaire (CTQ)" },
  { expId: 227, expCohort: 0, expWave: 0, expType: 10, expSubject: 1, expName: "Demographics", expInfo: "Household, background, language, education, family relations, economic situation, religion (or updates in wave Rondom 0)" },
  { expId: 228, expCohort: 0, expWave: 3, expType: 10, expSubject: 1, expName: "Demographics", expInfo: "Household, background, language, education, family relations, economic situation, religion (or updates in wave Rondom 0)" },
  { expId: 229, expCohort: 0, expWave: 4, expType: 10, expSubject: 1, expName: "Demographics", expInfo: "Household, background, language, education, family relations, economic situation, religion (or updates in wave Rondom 0)" },
  { expId: 230, expCohort: 0, expWave: 5, expType: 10, expSubject: 1, expName: "Demographics", expInfo: "Household, background, language, education, family relations, economic situation, religion (or updates in wave Rondom 0)" },
  { expId: 231, expCohort: 0, expWave: 0, expType: 10, expSubject: 1, expName: "Family illness", expInfo: "Medical & Psychiatric problems of first degree family members" },
  { expId: 232, expCohort: 0, expWave: 4, expType: 10, expSubject: 1, expName: "Family illness", expInfo: "Medical & Psychiatric problems of first degree family members" },
  { expId: 233, expCohort: 0, expWave: 5, expType: 10, expSubject: 1, expName: "Family illness", expInfo: "Medical & Psychiatric problems of first degree family members" },
  { expId: 234, expCohort: 0, expWave: 0, expType: 10, expSubject: 1, expName: "General health", expInfo: "General health questionnaire" },
  { expId: 235, expCohort: 0, expWave: 4, expType: 10, expSubject: 1, expName: "General health", expInfo: "General health questionnaire" },
  { expId: 236, expCohort: 0, expWave: 0, expType: 10, expSubject: 1, expName: "Major life events", expInfo: "Major life events in the past 12 months" },
  { expId: 237, expCohort: 0, expWave: 1, expType: 10, expSubject: 1, expName: "Major life events", expInfo: "Major life events in the past 12 months" },
  { expId: 238, expCohort: 0, expWave: 3, expType: 10, expSubject: 1, expName: "Major life events", expInfo: "Major life events in the past 12 months" },
  { expId: 239, expCohort: 0, expWave: 4, expType: 10, expSubject: 1, expName: "Major life events", expInfo: "Major life events in the past 12 months" },
  { expId: 240, expCohort: 0, expWave: 5, expType: 10, expSubject: 1, expName: "Major life events", expInfo: "Major life events in the past 12 months" },
  { expId: 241, expCohort: 0, expWave: 0, expType: 10, expSubject: 1, expName: "Lifestyle", expInfo: "Medication, exposure prior to pregnancy, alcohol, smoking, substance (ab)use" },
  { expId: 242, expCohort: 0, expWave: 3, expType: 10, expSubject: 1, expName: "Lifestyle", expInfo: "Medication, exposure prior to pregnancy, alcohol, smoking, substance (ab)use" },
  { expId: 243, expCohort: 0, expWave: 4, expType: 10, expSubject: 1, expName: "Lifestyle", expInfo: "Medication, exposure prior to pregnancy, alcohol, smoking, substance (ab)use" },
  { expId: 244, expCohort: 0, expWave: 5, expType: 10, expSubject: 1, expName: "Lifestyle", expInfo: "Medication, exposure prior to pregnancy, alcohol, smoking, substance (ab)use" },
  { expId: 245, expCohort: 0, expWave: 1, expType: 10, expSubject: 1, expName: "Personality: NEO-FFI-3", expInfo: "Personality questionnaire (NEO-FFI-3)" },
  { expId: 246, expCohort: 0, expWave: 1, expType: 10, expSubject: 1, expName: "Portrait values questionnaire - revised", expInfo: "Portrait values questionnaire - revised (PVQ-RR)" },
  { expId: 247, expCohort: 0, expWave: 1, expType: 10, expSubject: 1, expName: "Social Responsiveness Scale for Adults", expInfo: "Social Responsiveness Scale for Adults (SRS-A)" },
  { expId: 248, expCohort: 0, expWave: 1, expType: 10, expSubject: 1, expName: "Coping with situations", expInfo: "Utrechtse Coping Lijst (UCL)" },
  { expId: 249, expCohort: 0, expWave: 1, expType: 10, expSubject: 1, expName: "Work", expInfo: "Work demographics" },
  { expId: 250, expCohort: 0, expWave: 4, expType: 10, expSubject: 1, expName: "Work", expInfo: "Work demographics" },
  { expId: 251, expCohort: 0, expWave: 5, expType: 10, expSubject: 1, expName: "Work", expInfo: "Work demographics" },
  { expId: 252, expCohort: 0, expWave: 0, expType: 10, expSubject: 2, expName: "Adult Self Report", expInfo: "Adult Self Report (ASR)" },
  { expId: 253, expCohort: 0, expWave: 5, expType: 10, expSubject: 2, expName: "Adult Self Report", expInfo: "Adult Self Report (ASR)" },
  { expId: 254, expCohort: 0, expWave: 0, expType: 10, expSubject: 2, expName: "Brief Symptom Inventory", expInfo: "Brief Symptom Inventory (BSI)" },
  { expId: 255, expCohort: 0, expWave: 0, expType: 10, expSubject: 2, expName: "Periconceptual health", expInfo: "Periconceptual health" },
  { expId: 256, expCohort: 0, expWave: 1, expType: 10, expSubject: 2, expName: "Childhood trauma questionnaire", expInfo: "Childhood Trauma Questionnaire (CTQ)" },
  { expId: 257, expCohort: 0, expWave: 0, expType: 10, expSubject: 2, expName: "Demographics", expInfo: "Household, background, language, education, family relations, economic situation, religion (or updates in wave Rondom 0)" },
  { expId: 258, expCohort: 0, expWave: 3, expType: 10, expSubject: 2, expName: "Demographics", expInfo: "Household, background, language, education, family relations, economic situation, religion (or updates in wave Rondom 0)" },
  { expId: 259, expCohort: 0, expWave: 4, expType: 10, expSubject: 2, expName: "Demographics", expInfo: "Household, background, language, education, family relations, economic situation, religion (or updates in wave Rondom 0)" },
  { expId: 260, expCohort: 0, expWave: 5, expType: 10, expSubject: 2, expName: "Demographics", expInfo: "Household, background, language, education, family relations, economic situation, religion (or updates in wave Rondom 0)" },
  { expId: 261, expCohort: 0, expWave: 3, expType: 10, expSubject: 2, expName: "Edinburg Postnatal Depression Scale", expInfo: "Edinburgh Postnatal Depression Scale (EPDS)" },
  { expId: 262, expCohort: 0, expWave: 0, expType: 10, expSubject: 2, expName: "Food Frequency Questionnaire Pregnancy", expInfo: "Food intake questionnaire (FFQ) focussed on intake of energy, macronutrients, n-3 fatty acids, vitamin D, B-vitamins and folac acid during pregnancy" },
  { expId: 263, expCohort: 0, expWave: 0, expType: 10, expSubject: 2, expName: "Family illness", expInfo: "Medical & Psychiatric problems of first degree family members" },
  { expId: 264, expCohort: 0, expWave: 4, expType: 10, expSubject: 2, expName: "Family illness", expInfo: "Medical & Psychiatric problems of first degree family members" },
  { expId: 265, expCohort: 0, expWave: 5, expType: 10, expSubject: 2, expName: "Family illness", expInfo: "Medical & Psychiatric problems of first degree family members" },
  { expId: 266, expCohort: 0, expWave: 0, expType: 10, expSubject: 2, expName: "General health", expInfo: "General health questionnaire" },
  { expId: 267, expCohort: 0, expWave: 4, expType: 10, expSubject: 2, expName: "General health", expInfo: "General health questionnaire" },
  { expId: 268, expCohort: 0, expWave: 2, expType: 10, expSubject: 2, expName: "Labour and Birth", expInfo: "Labour and Birth" },
  { expId: 269, expCohort: 0, expWave: 0, expType: 10, expSubject: 2, expName: "Major life events", expInfo: "Major life events in the past 12 months" },
  { expId: 270, expCohort: 0, expWave: 1, expType: 10, expSubject: 2, expName: "Major life events", expInfo: "Major life events in the past 12 months" },
  { expId: 271, expCohort: 0, expWave: 3, expType: 10, expSubject: 2, expName: "Major life events", expInfo: "Major life events in the past 12 months" },
  { expId: 272, expCohort: 0, expWave: 4, expType: 10, expSubject: 2, expName: "Major life events", expInfo: "Major life events in the past 12 months" },
  { expId: 273, expCohort: 0, expWave: 5, expType: 10, expSubject: 2, expName: "Major life events", expInfo: "Major life events in the past 12 months" },
  { expId: 274, expCohort: 0, expWave: 0, expType: 10, expSubject: 2, expName: "Lifestyle", expInfo: "Vitamins, medication, exposure during pregnancy, alcohol, smoking, substance (ab)use, physical activity, sleep (PSQI)" },
  { expId: 275, expCohort: 0, expWave: 1, expType: 10, expSubject: 2, expName: "Lifestyle", expInfo: "Vitamins, medication, exposure during pregnancy, alcohol, smoking, substance (ab)use, physical activity, sleep (PSQI)" },
  { expId: 276, expCohort: 0, expWave: 3, expType: 10, expSubject: 2, expName: "Lifestyle", expInfo: "Vitamins, medication, exposure during pregnancy, alcohol, smoking, substance (ab)use, physical activity, sleep (PSQI)" },
  { expId: 277, expCohort: 0, expWave: 4, expType: 10, expSubject: 2, expName: "Lifestyle", expInfo: "Vitamins, medication, exposure during pregnancy, alcohol, smoking, substance (ab)use, physical activity, sleep (PSQI)" },
  { expId: 278, expCohort: 0, expWave: 5, expType: 10, expSubject: 2, expName: "Lifestyle", expInfo: "Vitamins, medication, exposure during pregnancy, alcohol, smoking, substance (ab)use, physical activity, sleep (PSQI)" },
  { expId: 279, expCohort: 0, expWave: 0, expType: 10, expSubject: 2, expName: "List of longterm stressful life events", expInfo: "List of longterm stressful life events selected by GenerationR" },
  { expId: 280, expCohort: 0, expWave: 1, expType: 10, expSubject: 2, expName: "Personality: NEO-FFI-3", expInfo: "Personality questionnaire (NEO-FFI-3)" },
  { expId: 281, expCohort: 0, expWave: 1, expType: 10, expSubject: 2, expName: "Portrait values questionnaire - revised", expInfo: "Portrait values questionnaire - revised (PVQ-RR)" },
  { expId: 282, expCohort: 0, expWave: 1, expType: 10, expSubject: 2, expName: "Social Responsiveness Scale for Adults", expInfo: "Social Responsiveness Scale for Adults (SRS-A)" },
  { expId: 283, expCohort: 0, expWave: 3, expType: 10, expSubject: 2, expName: "Social support list", expInfo: "Social Support List (SSL)" },
  { expId: 284, expCohort: 0, expWave: 1, expType: 10, expSubject: 2, expName: "Coping with situations", expInfo: "Utrechtse Coping Lijst (UCL)" },
  { expId: 285, expCohort: 0, expWave: 1, expType: 10, expSubject: 2, expName: "Work", expInfo: "Work demographics" },
  { expId: 286, expCohort: 0, expWave: 4, expType: 10, expSubject: 2, expName: "Work", expInfo: "Work demographics" },
  { expId: 287, expCohort: 0, expWave: 5, expType: 10, expSubject: 2, expName: "Work", expInfo: "Work demographics" },
  { expId: 288, expCohort: 0, expWave: 3, expType: 10, expSubject: 3, expName: "Ages and Stages Questionnaire - Social Emotional", expInfo: "Ages and Stages Questionnaire - Social Emotional (ASQ-SE)" },
  { expId: 289, expCohort: 0, expWave: 4, expType: 10, expSubject: 3, expName: "Ages and Stages Questionnaire - Social Emotional", expInfo: "Ages and Stages Questionnaire - Social Emotional (ASQ-SE)" },
  { expId: 290, expCohort: 0, expWave: 5, expType: 10, expSubject: 3, expName: "Ages and Stages Questionnaire - Social Emotional", expInfo: "Ages and Stages Questionnaire - Social Emotional (ASQ-SE)" },
  { expId: 291, expCohort: 0, expWave: 3, expType: 10, expSubject: 3, expName: "Daily care", expInfo: "Daily care of the child" },
  { expId: 292, expCohort: 0, expWave: 4, expType: 10, expSubject: 3, expName: "Daily care", expInfo: "Daily care of the child" },
  { expId: 293, expCohort: 0, expWave: 5, expType: 10, expSubject: 3, expName: "Daily care", expInfo: "Daily care of the child" },
  { expId: 294, expCohort: 0, expWave: 5, expType: 10, expSubject: 3, expName: "Child Behavior Checklist", expInfo: "Child Behavior Checklist (CBCL). Questionnaire about problem behavior and skills of the child" },
  { expId: 295, expCohort: 0, expWave: 3, expType: 10, expSubject: 3, expName: "Child Behavior Questionnaire", expInfo: "Behavior Questionnaire (IBQ-R SF, ECBQ, CBQ, TMCQ)" },
  { expId: 296, expCohort: 0, expWave: 4, expType: 10, expSubject: 3, expName: "Child Behavior Questionnaire", expInfo: "Behavior Questionnaire (IBQ-R SF, ECBQ, CBQ, TMCQ)" },
  { expId: 297, expCohort: 0, expWave: 5, expType: 10, expSubject: 3, expName: "Child Behavior Questionnaire", expInfo: "Behavior Questionnaire (IBQ-R SF, ECBQ, CBQ, TMCQ)" },
  { expId: 298, expCohort: 0, expWave: 3, expType: 10, expSubject: 3, expName: "Comprehensive Early Childhood Parenting Questionnaire", expInfo: "Comprehensive Early Childhood Parenting Questionnaire (CECPAQ)" },
  { expId: 299, expCohort: 0, expWave: 4, expType: 10, expSubject: 3, expName: "Comprehensive Early Childhood Parenting Questionnaire", expInfo: "Comprehensive Early Childhood Parenting Questionnaire (CECPAQ)" },
  { expId: 300, expCohort: 0, expWave: 5, expType: 10, expSubject: 3, expName: "Comprehensive Early Childhood Parenting Questionnaire", expInfo: "Comprehensive Early Childhood Parenting Questionnaire (CECPAQ)" },
  { expId: 301, expCohort: 0, expWave: 5, expType: 10, expSubject: 3, expName: "Language situation and pragmatics", expInfo: "Clinical Evaluation of Language Fundamentals Preschool (CELF - Preschool-2-NL)" },
  { expId: 302, expCohort: 0, expWave: 3, expType: 10, expSubject: 3, expName: "Food Frequency Questionnaire YOUth", expInfo: "Food Frequency Questionnaire (FFQ)" },
  { expId: 303, expCohort: 0, expWave: 4, expType: 10, expSubject: 3, expName: "Food Frequency Questionnaire YOUth", expInfo: "Food Frequency Questionnaire (FFQ)" },
  { expId: 304, expCohort: 0, expWave: 5, expType: 10, expSubject: 3, expName: "Food Frequency Questionnaire YOUth", expInfo: "Food Frequency Questionnaire (FFQ)" },
  { expId: 305, expCohort: 0, expWave: 4, expType: 10, expSubject: 3, expName: "Antropometrics and vaccinations", expInfo: "Length, head circumference, weight and vaccinations" },
  { expId: 306, expCohort: 0, expWave: 5, expType: 10, expSubject: 3, expName: "Antropometrics and vaccinations", expInfo: "Length, head circumference, weight and vaccinations" },
  { expId: 307, expCohort: 0, expWave: 3, expType: 10, expSubject: 3, expName: "Child health", expInfo: "Medical questionnaire on child's health and Gender Identity (GI)" },
  { expId: 308, expCohort: 0, expWave: 4, expType: 10, expSubject: 3, expName: "Child health", expInfo: "Medical questionnaire on child's health and Gender Identity (GI)" },
  { expId: 309, expCohort: 0, expWave: 5, expType: 10, expSubject: 3, expName: "Child health", expInfo: "Medical questionnaire on child's health and Gender Identity (GI)" },
  { expId: 310, expCohort: 0, expWave: 5, expType: 10, expSubject: 3, expName: "Interpersonal Reactivity Index", expInfo: "Interpersonal Reactivity Index (IRI)" },
  { expId: 311, expCohort: 0, expWave: 5, expType: 10, expSubject: 3, expName: "The Infant-Toddler Social & Emotional Assessment-Revised (ITSEA) ", expInfo: "The Infant-Toddler Social & Emotional Assessment-Revised (ITSEA) " },
  { expId: 312, expCohort: 0, expWave: 3, expType: 10, expSubject: 3, expName: "Language situation", expInfo: "Spoken language in child's environment" },
  { expId: 313, expCohort: 0, expWave: 4, expType: 10, expSubject: 3, expName: "Language situation", expInfo: "Spoken language in child's environment" },
  { expId: 314, expCohort: 0, expWave: 4, expType: 10, expSubject: 3, expName: "Language development", expInfo: "Nederlandse - Communicative Development Inventories (N-CDI-1, N-CDI-2)" },
  { expId: 315, expCohort: 0, expWave: 5, expType: 10, expSubject: 3, expName: "Language development", expInfo: "Nederlandse - Communicative Development Inventories (N-CDI-1, N-CDI-2)" },
  { expId: 316, expCohort: 0, expWave: 5, expType: 10, expSubject: 3, expName: "Leisure time", expInfo: "Sports and hobbies created by GenerationR" },
  { expId: 317, expCohort: 0, expWave: 5, expType: 10, expSubject: 3, expName: "Media use", expInfo: "Use of apps, television,  games and books" },
  { expId: 318, expCohort: 0, expWave: 3, expType: 10, expSubject: 3, expName: "Parental Stress Index - Sense of Competence", expInfo: "Nijmeegse Ouderlijke Stress Index (NOSI)/Parental Stress Index (PSI) - subscale Sense of competence" },
  { expId: 319, expCohort: 0, expWave: 4, expType: 10, expSubject: 3, expName: "Parental Stress Index - Sense of Competence", expInfo: "Nijmeegse Ouderlijke Stress Index (NOSI)/Parental Stress Index (PSI) - subscale Sense of competence" },
  { expId: 320, expCohort: 0, expWave: 5, expType: 10, expSubject: 3, expName: "Parental Stress Index - Sense of Competence", expInfo: "Nijmeegse Ouderlijke Stress Index (NOSI)/Parental Stress Index (PSI) - subscale Sense of competence" },
  { expId: 321, expCohort: 0, expWave: 5, expType: 10, expSubject: 3, expName: "Strengths and difficulties questionnaire", expInfo: "Strengths and difficulties questionnaire (SDQ)" },
  { expId: 322, expCohort: 0, expWave: 5, expType: 11, expSubject: 0, expName: "Delay Gratification", expInfo: "" },
  { expId: 323, expCohort: 0, expWave: 5, expType: 11, expSubject: 0, expName: "Hand Game", expInfo: "" }
];

const chrtOptions = { 0: 'YOUth Baby and Child',
                      1: 'YOUth Child and Adolescent' };
const waveOptions = { 0: '20 weeks pregnancy',
                      1: '30 weeks pregnancy',
                      2: 'Around 0 months',
                      3: 'Around 5 months',
                      4: 'Around 10 months',
                      5: 'Around 3 years',
                      6: 'Around 6 years old',
                      7: 'Around 9 years',
                      8: 'Around 12 years',
                      9: 'Around 15 years' };
const typeOptions = { 0: 'Biological material',
                      1: 'Body measures',
                      2: 'Computer task',
                      3: 'Echo',
                      4: 'EEG',
                      5: 'Eyetracking',
                      6: 'Intelligence quotient',
                      7: 'Mock scanner',
                      8: 'MRI',
                      9: 'Parent Child Interaction',
                      10: 'Questionnaire',
                      11: 'Video task' };
const subjOptions = { 0: 'Child',
                      1: 'Father',
                      2: 'Mother',
                      3: 'Parent/tutor about child',
                      4: 'Partner',
                      5: 'Teacher about child' };

const expandRow = {
  showExpandColumn: true,
  renderer: row => (
    <div>
      <p>{ `${row.expInfo}` }</p>
    </div>
  )
};

const columns = [
{
  dataField: 'expId',
  text: 'ID'
}, {
  dataField: 'expName',
  text: 'Experiment name',
  filter: textFilter()
}, {
  dataField: 'expType',
  text: 'Type',
  formatter: cell => typeOptions[cell],
  filter: multiSelectFilter({
    options: typeOptions,
    comparator: Comparator.EQ
  })
}, {
  dataField: 'expCohort',
  text: 'Cohort',
  formatter: cell => chrtOptions[cell],
  filter: selectFilter({
    options: chrtOptions
  })
}, {
  dataField: 'expWave',
  text: 'Wave',
  formatter: cell => waveOptions[cell],
  filter: multiSelectFilter({
    options: waveOptions
  })
}, {
  dataField: 'expSubject',
  text: 'Subject',
  formatter: cell => subjOptions[cell],
  filter: multiSelectFilter({
    options: subjOptions
  })
}
];

const cartColumns = [
  {
    dataField: 'expId',
    text: 'ID'
  }, {
    dataField: 'expName',
    text: 'Experiment name'
  }, {
    dataField: 'expType',
    text: 'Type',
    formatter: cell => typeOptions[cell]
  }, {
    dataField: 'expCohort',
    text: 'Cohort',
    formatter: cell => chrtOptions[cell]
  }, {
    dataField: 'expWave',
    text: 'Wave',
    formatter: cell => waveOptions[cell],
  }, {
    dataField: 'expSubject',
    text: 'Subject',
    formatter: cell => subjOptions[cell],
  }
];

const paginationOptions = {
  sizePerPageList: [{
    text: '10', value: 10
  }, {
    text: '50', value: 50
  }, {
    text: 'All', value: data.length
  }]
}

class DataSelectionCart extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      ...props.formData
    }
  }
  render() {
    return(
      <div>
        <h2>🛒</h2>
        <BootstrapTable data = { this.state.selectedRows }
                        columns = { cartColumns }
                        expandRow = { expandRow }
                        keyField = 'expId'
                        noDataIndication={ 'No data sets selected yet.' } />
      </div>
    );
  }
}

class DataSelectionTable extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      selectedRows: [],
      ...props.formData
    };
  }

  selectRow = (row, isSelect) => {
    let selectedRows = this.state.selectedRows;
    if (isSelect) {
      selectedRows.push(row);
    } else {
      selectedRows = selectedRows.filter(selectedRows => selectedRows.expId != row.expId);
    }
    this.setState({'selectedRows': selectedRows}, () => this.props.onChange(this.state));
  };

  getSelectedRowIds() {
    let selectedRowIds = [];
    this.state.selectedRows.forEach(function(row, index) {
      selectedRowIds.push(row['expId']);
    });
    return selectedRowIds;
  }

  render() {
    const selectRow = {
      mode: "checkbox",
      clickToSelect: true,
      hideSelectAll: true,
      selected: this.getSelectedRowIds(),
      style: { backgroundColor: '#c8e6c9' },
      onSelect: this.selectRow
    };

    return (
      <div>
        <BootstrapTable ref={ n => this.node = n }
                        keyField='expId'
                        data={ data }
                        columns={ columns }
                        expandRow={ expandRow }
                        selectRow={ selectRow }
                        pagination={ paginationFactory(paginationOptions) }
                        filter={ filterFactory() } />
        <h2>🛒</h2>
        <BootstrapTable data = { this.state.selectedRows }
                        columns = { cartColumns }
                        expandRow = { expandRow }
                        keyField = 'expId'
                        noDataIndication={ 'No data sets selected yet.' } />
      </div>
    );
  }
}

export { DataSelectionTable, DataSelectionCart };
