import {createRouter, createWebHistory} from 'vue-router';

import ApprovalBackups                      from '../approvals/Backups.vue';
import ApprovalBatches                      from '../approvals/Batches.vue';
import ApprovalDashboard                    from '../approvals/Dashboard.vue';
import ApprovalDocument                     from '../approvals/Document.vue';
import ApprovalDocuments                    from '../approvals/Documents.vue';
import ApprovalExpenses                     from '../approvals/Expenses.vue';
import ApprovalInvoices                     from '../approvals/Invoices.vue';
import ApprovalJobCompletions               from '../approvals/JobCompletions.vue';
import ApprovalOrderReturn                  from '../approvals/OrderReturn.vue';
import ApprovalOrderReturns                 from '../approvals/OrderReturns.vue';
import ApprovalPurchaseOrders               from '../approvals/PurchaseOrders.vue';
import ApprovalPurchaseRequests             from '../approvals/PurchaseRequests.vue';
//import ApprovalPurchaseRequests             from '../approvals/PurchaseRequests.vue';
import ApprovalSalesOrder                   from '../approvals/SalesOrder.vue';
import ApprovalSalesOrders                  from '../approvals/SalesOrders.vue';

import ApprovalWorkOrders                   from '../approvals/WorkOrders.vue';

    import ApprovalFormAction                   from '../approvals/forms/Action.vue';
    import ApprovalFormInvoice                  from '../approvals/forms/Invoice.vue';
    import ApprovalFormSalesOrder               from '../approvals/forms/SalesOrder.vue';
    import ApprovalFormSalesQuotation           from '../approvals/forms/SalesQuotation.vue';

import ArchiveBackups                       from '../archives/Backups.vue';
import ArchiveCategories                    from '../archives/Categories.vue';
import ArchiveCategory                      from '../archives/Category.vue';
import ArchiveDashboard                     from '../archives/Dashboard.vue';
import ArchiveDocuments                     from '../archives/Documents.vue';
import ArchiveDocument                      from '../archives/Document.vue';

    import ArchiveDetailBackupList          from '../archives/details/BackupList.vue';
    import ArchiveDetailCategoryList        from '../archives/details/CategoryList.vue';
    import ArchiveDetailDocumentList        from '../archives/details/DocumentList.vue';

    import ArchiveFormBackup                from '../archives/forms/Backup.vue';
    import ArchiveFormCategory              from '../archives/forms/Category.vue';
    import ArchiveFormDocument              from '../archives/forms/Document.vue';
    import ArchiveFormDocumentSearch        from '../archives/forms/DocumentSearch.vue';

import ChatDashboard                        from '../chat/Dashboard.vue';
import ChatCompose                          from '../chat/Compose.vue';
import ChatInbox                            from '../chat/Inbox.vue';
import ChatMessage                          from '../chat/Message.vue';
import ChatMessages                         from '../chat/Messages.vue';
import ChatOutbox                           from '../chat/Outbox.vue';
import ChatRooms                            from '../chat/Rooms.vue';
import ChatRoom                             from '../chat/Room.vue';

    import ChatDetailMessageList                       from '../chat/details/MessageList.vue';
    import ChatDetailMessageView                       from '../chat/details/MessageView.vue';
    import ChatDetailRoomList                          from '../chat/details/RoomList.vue';

    import ChatFormMessage                             from '../chat/forms/Message.vue';

import CRMCustomer                          from '../crm/Customer.vue';
import CRMCustomers                         from '../crm/Customers.vue';
import CRMDashboard                         from '../crm/Dashboard.vue';
import CRMLead                              from '../crm/Lead.vue';
import CRMLeads                             from '../crm/Leads.vue';

    import CRMDetailContactList                             from '../crm/details/ContactList.vue';
    import CRMDetailCustomer                                from '../crm/details/Customer.vue';
    import CRMDetailCustomerList                            from '../crm/details/CustomerList.vue';
    import CRMDetailCustomerSummary                         from '../crm/details/CustomerSummary.vue';
    import CRMDetailLead                                    from '../crm/details/Lead.vue';
    import CRMDetailLeadList                                from '../crm/details/LeadList.vue';

    import CRMFormContact                                   from '../crm/forms/Contact.vue';
    import CRMFormCustomer                                  from '../crm/forms/Customer.vue';
    import CRMFormCustomerUpload                            from '../crm/forms/CustomerUpload.vue';
    import CRMFormLead                                      from '../crm/forms/Lead.vue';
    
import DashboardMain        from '../dashboard/Main.vue';

import EMRConsultantConsultation           from '../emr/consultant/Consultation.vue';
import EMRConsultantDashboard              from '../emr/consultant/Dashboard.vue';
import EMRConsultantQueue                  from '../emr/consultant/Queue.vue';
import EMRConsultantQueueDepartment        from '../emr/consultant/Queue.vue';
import EMRConsultantQueueMy                from '../emr/consultant/Queue.vue';
import EMRConsultantQueueDoctor            from '../emr/consultant/QueueDoctor.vue';
import EMRConsultantMyPastConsultations    from '../emr/consultant/MyPastConsultations.vue';

    import EMRConsultantDetailConsultation    from '../emr/consultant/details/Consultation.vue';
    import EMRConsultantDetailQueue           from '../emr/consultant/details/Queue.vue';
    import EMRConsultantDetailQueueList       from '../emr/consultant/details/QueueList.vue';
    import EMRConsultantDetailReview          from '../emr/consultant/details/Review.vue';  
    import EMRConsultantDetailSummary         from '../emr/consultant/details/Summary.vue';
    import EMRConsultantDetailResultQueue     from '../emr/consultant/details/ResultQueue.vue';

    import EMRConsultantFormConsultation        from '../emr/consultant/forms/Consultation.vue';
    import EMRConsultantFormHistory             from '../emr/consultant/forms/History.vue';
    import EMRConsultantFormLaboratory          from '../emr/consultant/forms/Laboratory.vue';
    import EMRConsultantFormPrescription        from '../emr/consultant/forms/Prescription.vue';
    import EMRConsultantFormRadiology           from '../emr/consultant/forms/Radiology.vue';
    import EMRConsultantFormSoapNote            from '../emr/consultant/forms/SoapNote.vue';

import EMRFrontOfficeDashboard                  from '../emr/front_office/Dashboard.vue';
import EMRFrontOfficeAppointment                from '../emr/front_office/Appointment.vue';
import EMRFrontOfficeAppointments               from '../emr/front_office/Appointments.vue';

    import EMRFrontOfficeFormAppointment            from '../emr/front_office/forms/Appointment.vue';
    import EMRFrontOfficeFormCheckIn                from '../emr/front_office/forms/CheckIn.vue';

import EMRPatientAll                            from '../emr/patients/All.vue';
import EMRPatientAllergies                      from '../emr/patients/Allergies.vue';  
import EMRPatientContacts                       from '../emr/patients/Contacts.vue'; 
import EMRPatientPrescriptions                  from '../emr/patients/Prescriptions.vue';  
import EMRPatientSearch                         from '../emr/patients/Search.vue';
import EMRPatientSingle                         from '../emr/patients/Single.vue';  
import EMRPatientVitals                         from '../emr/patients/Vitals.vue'; 

    //import EMRPatientFormAllergy                       from '../emr/patients/forms/Allergy.vue';
    //import EMRPatientFormContact                       from '../emr/patients/forms/Contact.vue';
    //import EMRFormPatientService              from '../emr/hims/forms/PatientService.vue';

    import EMRPatientDetailAllergies                   from '../emr/patients/details/Allergies.vue';
    import EMRPatientDetailBioData                     from '../emr/patients/details/BioData.vue';
    import EMRPatientDetailCard                        from '../emr/patients/details/Card.vue';
    import EMRPatientDetailFull                        from '../emr/patients/details/Full.vue';
    import EMRPatientDetailNextOfKin                   from '../emr/patients/details/NextOfKin.vue';
    import EMRPatientDetailPendingTransactions         from '../emr/patients/details/PendingTransactions.vue';
    import EMRPatientDetailPatientList                 from '../emr/patients/details/PatientList.vue';
    import EMRPatientDetailInsurances                  from '../emr/patients/details/Insurances.vue';
    
    import EMRPatientFormAllergy                        from '../emr/patients/forms/Allergy.vue';
    import EMRPatientFormContact                        from '../emr/patients/forms/Contact.vue';
    import EMRPatientFormInsurance                      from '../emr/patients/forms/Insurance.vue';
    import EMRPatientFormPassword                       from '../emr/patients/forms/Password.vue';
    import EMRPatientFormPatient                        from '../emr/patients/forms/Patient.vue';   
    import EMRPatientFormPrescription                   from '../emr/patients/forms/Prescription.vue';
    import EMRPatientFormRegistration                   from '../emr/patients/forms/Registration.vue';
    import EMRPatientFormSearch                         from '../emr/patients/forms/Search.vue';
    import EMRPatientFormVital                          from '../emr/patients/forms/Vital.vue';


// EMRPatientFormRegistration
import EMRLaboratoryCollect                    from '../emr/laboratory/Collect.vue';
import EMRLaboratoryDashboard                  from '../emr/laboratory/Dashboard.vue';
import EMRLaboratoryPOS                        from '../emr/laboratory/POS.vue';
import EMRLaboratoryQueue                      from '../emr/laboratory/Queue.vue';

import EMRNursingAdmissionRequests              from '../emr/nursing/AdmissionRequests.vue';
import EMRNursingDashboard                      from '../emr/nursing/Dashboard.vue';
import EMRNursingVitals                         from '../emr/nursing/Vitals.vue';

//import EMROperationsDashboard                   from '../emr/operations/Dashboard.vue';
import EMROperationsBranch                      from '../emr/operations/Branch.vue';      
import EMROperationsBranches                    from '../emr/operations/Branches.vue'; 
import EMROperationsBranchPriceList             from '../emr/operations/BranchPriceLists.vue';    
import EMROperationsDashboard                   from '../emr/operations/Dashboard.vue';   
import EMROperationsDepartments                 from '../emr/operations/Departments.vue'; 
import EMROperationsPricelist                   from '../emr/operations/Pricelist.vue';   
import EMROperationsPricelists                  from '../emr/operations/Pricelists.vue';  
import EMROperationsService                     from '../emr/operations/Service.vue';     
import EMROperationsServices                    from '../emr/operations/Services.vue';    
import EMROperationsServiceTypes                from '../emr/operations/ServiceTypes.vue';

import EMRRadiologyDashboard                    from '../emr/radiology/Dashboard.vue';
import EMRRadiologyInsurance                    from '../emr/radiology/Insurance.vue'
import EMRRadiologyQueue                        from '../emr/radiology/Queue.vue';
import EMRRadiologyReferredIn                   from '../emr/radiology/ReferredIn.vue';
import EMRRadiologyReferredOut                  from '../emr/radiology/ReferredOut.vue';

    import EMRRadiologyDetailReferralList           from '../emr/radiology/details/ReferralList.vue'
    import EMRRadiologyDetailRequestList            from '../emr/radiology/details/RequestList.vue';

import EMRVisitAll                              from '../emr/visitations/All.vue';
import EMRVisitBill                             from '../emr/visitations/Bill.vue';
import EMRVisitDashboard                        from '../emr/visitations/Dashboard.vue';
import EMRVisitSingle                           from '../emr/visitations/Single.vue';

    import EMRVisitDetailList                   from '../emr/visitations/details/List.vue';
    import EMRVisitDetailSummary                from '../emr/visitations/details/Summary.vue';


import EquipmentAsset                           from '../equipments/Asset.vue';
import EquipmentAssets                          from '../equipments/Assets.vue';
import EquipmentDashboard                       from '../equipments/Dashboard.vue';
import EquipmentMaintenance                     from '../equipments/Maintenance.vue';
import EquipmentMaintenances                    from '../equipments/Maintenances.vue';
import EquipmentSchedule                        from '../equipments/Schedule.vue';
import EquipmentSchedules                       from '../equipments/Schedules.vue';

    import EquipmentDetailAsset                     from '../equipments/details/Asset.vue';
    import EquipmentDetailAssetList                 from '../equipments/details/AssetList.vue';
    import EquipmentDetailMaintenance               from '../equipments/details/Maintenance.vue';
    import EquipmentDetailMaintenanceList           from '../equipments/details/MaintenanceList.vue';
    import EquipmentDetailSchedule                  from '../equipments/details/Schedule.vue';
    import EquipmentDetailScheduleList              from '../equipments/details/ScheduleList.vue';
    import EquipmentDetailTransfer                  from '../equipments/details/Transfer.vue';
    import EquipmentDetailTransferList              from '../equipments/details/TransferList.vue';

    import EquipmentFormAsset                     from '../equipments/forms/Asset.vue';
    import EquipmentFormMaintenance               from '../equipments/forms/Maintenance.vue';
    import EquipmentFormSchedule                  from '../equipments/forms/Schedule.vue';
    import EquipmentFormTransfer                  from '../equipments/forms/Transfer.vue';
    
import EscrowAdminDashboard                     from '../escrows/AdminDashboard.vue';
import EscrowAdminDispute                       from '../escrows/AdminDispute.vue';
import EscrowAdminDisputes                      from '../escrows/AdminDisputes.vue';
import EscrowAdminProduct                       from '../escrows/AdminProduct.vue';
import EscrowAdminProducts                      from '../escrows/AdminProducts.vue';
import EscrowAdminTransaction                   from '../escrows/AdminTransaction.vue';
import EscrowAdminTransactions                  from '../escrows/AdminTransactions.vue';

import EscrowDashboard                          from '../escrows/Dashboard.vue';
import EscrowDispute                            from '../escrows/Dispute.vue';
import EscrowDisputes                           from '../escrows/Disputes.vue';
import EscrowProduct                            from '../escrows/Product.vue';
import EscrowProducts                           from '../escrows/Products.vue';
import EscrowTransaction                        from '../escrows/Transaction.vue';
import EscrowTransactions                       from '../escrows/Transactions.vue';

    import EscrowDetailDispute                  from '../escrows/details/Dispute.vue';
    import EscrowDetailDisputeList              from '../escrows/details/DisputeList.vue';
    import EscrowDetailPayment                  from '../escrows/details/Payment.vue';
    import EscrowDetailPaymentList              from '../escrows/details/PaymentList.vue';
    import EscrowDetailProduct                  from '../escrows/details/Product.vue';
    import EscrowDetailProductList              from '../escrows/details/ProductList.vue';
    import EscrowDetailTransaction              from '../escrows/details/Transaction.vue';
    import EscrowDetailTransactionList          from '../escrows/details/TransactionList.vue';

    import EscrowFormAccept                     from '../escrows/forms/Accept.vue';
    import EscrowFormDispute                    from '../escrows/forms/Dispute.vue';
    import EscrowFormProduct                    from '../escrows/forms/Product.vue';
    import EscrowFormTransaction                from '../escrows/forms/Transaction.vue';
    import EscrowFormTransactionProduct         from '../escrows/forms/TransactionProduct.vue';
    import EscrowFormTransactionRequest         from '../escrows/forms/TransactionRequest.vue';
    /*
import EServiceAdminDashboard        from '../eservices/admin/Dashboard.vue';
import EServiceAdminReportDetailed   from '../eservices/admin/Detailed.vue';
import EServiceAdminReportHomeOffice from '../eservices/admin/HomeOffice.vue';
import EServiceAdminReportSummary    from '../eservices/admin/Summary.vue';

import EServiceCertificate           from '../eservices/certificates/Certificate.vue';
import EServiceCertificateBioData    from '../eservices/certificates/BioData.vue';
import EServiceCertificateFooter     from '../eservices/certificates/Footer.vue';
import EServiceCertificateHeader     from '../eservices/certificates/Header.vue';
import EServiceCertificateSummary    from '../eservices/certificates/Summary.vue';
import EServiceCertificateSummaryKid from '../eservices/certificates/SummaryKid.vue';
import EServiceCertificateSummaryLab from '../eservices/certificates/SummaryLab.vue';

import EServiceFrontAppointment      from '../eservices/front/Appointment.vue';
import EServiceFrontAppointments     from '../eservices/front/Appointments.vue';
import EServiceFrontCertificates     from '../eservices/front/Certificates.vue';
import EServiceFrontMissed           from '../eservices/front/Missed.vue';
import EServiceFrontPatients         from '../eservices/front/Patients.vue';
import EServicePayments              from '../eservices/front/Payments.vue';
import EServicePayment               from '../eservices/front_admin/Payment.vue';
import EServiceRadiographer          from '../eservices/front/Radiographer.vue';
import EServiceFrontReferral         from '../eservices/front/Referral.vue';

import EServiceFrontAdminPatients       from '../eservices/front_admin/Patients.vue';
import EServiceFrontAdminAppointment    from '../eservices/front_admin/Appointment.vue';
import EServiceFrontAdminAppointments   from '../eservices/front_admin/Appointments.vue';
import EServiceFrontAdminPayments       from '../eservices/front_admin/Payments.vue';

import EServiceDocConsultation       from '../eservices/doctor/Consultation.vue';
import EServiceDocConsultations      from '../eservices/doctor/Consultations.vue';
import EServiceDocConsultationSingle from '../eservices/doctor/ConsultationSingle.vue';
import EServiceDocConsentView        from '../eservices/doctor/ConsentView.vue';
import EServiceDocConsultationView   from '../eservices/doctor/ConsultationView.vue';
import EServiceDocPending            from '../eservices/doctor/Pending.vue';
import EServiceDocLaboratoryView     from '../eservices/doctor/LaboratoryView.vue';
import EServiceDocPatientView        from '../eservices/doctor/PatientView.vue';
import EServiceDocReportView         from '../eservices/doctor/ReportView.vue';
import EServiceDocReviews            from '../eservices/doctor/Reviews.vue';
import EServiceDocView               from '../eservices/doctor/View.vue';

import EServiceRadReport             from '../eservices/radiologist/Report.vue';
import EServiceRadReports            from '../eservices/radiologist/Reports.vue';
import EServiceRadReviews            from '../eservices/radiologist/Reviews.vue';

    import EServiceDetailAppointment        from '../eservices/details/Appointment.vue';
    import EServiceDetailAppointmentList    from '../eservices/details/AppointmentList.vue';
    import EServiceDetailIssueView          from '../eservices/details/IssueView.vue';
    import EServiceDetailPaymentList        from '../eservices/details/PaymentList.vue';
    import EServiceDetailReferral           from '../eservices/details/Referral.vue';
    import EServiceDetailReport             from '../eservices/details/Report.vue';

    import EServiceFormAppointment      from '../eservices/forms/Appointment.vue';
    import EServiceFormArrival          from '../eservices/forms/Arrival.vue';
    import EServiceFormPatient          from '../eservices/forms/Patient.vue';
    import EServiceFormPayment          from '../eservices/forms/Payment.vue';
    import EServiceFormReport           from '../eservices/forms/Report.vue';
    import EServiceFormSearch           from '../eservices/forms/Search.vue';

    import EServiceDocFormConsent              from '../eservices/doctor/forms/Consent.vue';
    import EServiceDocFormConsentPad           from '../eservices/doctor/forms/ConsentPad.vue';
    import EServiceDocFormIssue                from '../eservices/doctor/forms/Issue.vue';
    import EServiceDocFormLaboratory           from '../eservices/doctor/forms/Laboratory.vue';
    import EServiceDocFormReferral             from '../eservices/doctor/forms/Referral.vue';
    import EServiceDocFormScreening            from '../eservices/doctor/forms/Screening.vue';

import ExternalDone                         from '../external/Done.vue';
    import ExternalDetailPolicy                 from '../external/details/Policy.vue';
    import ExternalFormDirect                   from '../external/forms/Direct.vue';
    import ExternalFormReschedule               from '../external/forms/Reschedule.vue';

*/
import FacilityDashboard            from '../facility/Dashboard.vue';
import FacilitySpaces            from '../facility/Spaces.vue';
//import FacilitySpaces            from '../facility/Spaces.vue';

import FinanceAsset                 from '../finance/Asset.vue';
import FinanceAssets                from '../finance/Assets.vue';
import FinanceBranchAccounts        from '../finance/BranchAccounts.vue';
import FinanceBranchPriceList       from '../finance/BranchPriceList.vue';
import FinanceBranchPriceLists      from '../finance/BranchPriceLists.vue';
import FinanceCashRegister          from '../finance/CashRegister.vue';
import FinanceChartAccounts         from '../finance/ChartAccounts.vue';
import FinanceCustomers             from '../finance/Customers.vue';
import FinanceDashboard             from '../finance/Dashboard.vue';
import FinanceDeposits              from '../finance/Deposits.vue';
import FinanceExpense               from '../finance/Expense.vue';
import FinanceExpenses              from '../finance/Expenses.vue';
import FinanceExpenseTypes          from '../finance/ExpenseTypes.vue';
import FinanceInsurance             from '../finance/Insurance.vue';
import FinanceIncome                from '../finance/Income.vue';
import FinanceIncomes               from '../finance/Incomes.vue';
import FinanceInvoice               from '../finance/Invoice.vue';
import FinanceInvoices              from '../finance/Invoices.vue';
import FinancePatient               from '../finance/Patient.vue';
import FinancePayment               from '../finance/Payment.vue';
import FinancePayments              from '../finance/Payments.vue';
import FinancePayOut                from '../finance/PayOut.vue';
import FinancePayOuts               from '../finance/PayOuts.vue';
import FinancePricelist             from '../finance/Pricelist.vue';
import FinancePricelists            from '../finance/Pricelists.vue';
import FinancePaymentModes          from '../finance/PaymentModes.vue';
import FinanceTransaction           from '../finance/Transaction.vue';
import FinanceTransactions          from '../finance/Transactions.vue';
import FinanceReports               from '../finance/Reports.vue';
import FinanceVisit                 from '../finance/Visit.vue';

    
    import FinanceDetailAssetList                       from '../finance/details/AssetList.vue';
    import FinanceDetailBranchAccount                   from '../finance/details/BranchAccount.vue';
    import FinanceDetailBranchAccountList               from '../finance/details/BranchAccountList.vue';
    import FinanceDetailBranchPricelistList             from '../finance/details/BranchPricelistList.vue';
    import FinanceDetailExpense                         from '../finance/details/Expense.vue';
    import FinanceDetailExpenseList                     from '../finance/details/ExpenseList.vue';
    import FinanceDetailChartAccountList                from '../finance/details/ChartAccountList.vue';
    import FinanceDetailCustomerTransaction             from '../finance/details/CustomerTransaction.vue';
    import FinanceDetailCustomerTransactions            from '../finance/details/CustomerTransactions.vue';
    import FinanceDetailIncome                          from '../finance/details/Income.vue';
    import FinanceDetailIncomeList                      from '../finance/details/IncomeList.vue';
    import FinanceDetailInvoice                         from '../finance/details/Invoice.vue';
    import FinanceDetailInvoiceList                     from '../finance/details/InvoiceList.vue';
    import FinanceDetailPayment                         from '../finance/details/Payment.vue';
    import FinanceDetailPaymentAllocationList           from '../finance/details/PaymentAllocationList.vue';
    import FinanceDetailPaymentList                     from '../finance/details/PaymentList.vue';
    import FinanceDetailPayOut                          from '../finance/details/PayOut.vue';
    import FinanceDetailPayOutAllocationList            from '../finance/details/PayOutAllocationList.vue';
    import FinanceDetailPayOutList                      from '../finance/details/PayOutList.vue';
    import FinanceDetailPaymentModeList                 from '../finance/details/PaymentModeList.vue';
    import FinanceDetailPricelist                       from '../finance/details/Pricelist.vue';
    import FinanceDetailPricelistList                   from '../finance/details/PricelistList.vue';
    import FinanceDetailPricelistPlanList               from '../finance/details/PricelistPlanList.vue';
    import FinanceDetailReportAgingAnalysisReceivables  from '../finance/details/ReportAgingAnalysisReceivables.vue';
    import FinanceDetailReportBalanceSheet              from '../finance/details/ReportBalanceSheet.vue';
    import FinanceDetailTransaction                     from '../finance/details/Transaction.vue';
    import FinanceDetailTransactionList                 from '../finance/details/TransactionList.vue';  

    import FinanceFormBranchAccount                     from '../finance/forms/BranchAccount.vue';
    import FinanceFormBranchPricelist                   from '../finance/forms/BranchPriceList.vue';
    import FinanceFormExpense                           from '../finance/forms/Expense.vue';
    import FinanceFormExpenseType                       from '../finance/forms/ExpenseType.vue';
    import FinanceFormDeposit                           from '../finance/forms/Deposit.vue';
    import FinanceFormIncome                            from '../finance/forms/Income.vue';
    import FinanceFormInvoice                           from '../finance/forms/Invoice.vue';
    import FinanceFormPayment                           from '../finance/forms/Payment.vue';
    import FinanceFormPaymentMode                       from '../finance/forms/PaymentMode.vue';
    import FinanceFormPayOut                            from '../finance/forms/PayOut.vue';
    import FinanceFormPricelist                         from '../finance/forms/Pricelist.vue';
    import FinanceFormPricelistItemBulk                 from '../finance/forms/PricelistItemBulk.vue';
    import FinanceFormTransaction                       from '../finance/forms/Transaction.vue';
    
import GeneralChartBar                  from '../general/charts/Bar.vue';
import GeneralChartDonut                from '../general/charts/Donut.vue';
import GeneralFormTab                   from '../general/forms/Tab.vue';
import GeneralFormWizard                from '../general/forms/Wizard.vue';
    
import HeaderBranch                     from '../header/Branch.vue';
    
import HrmsDashboard                    from '../hrms/Dashboard.vue';
import HrmsDashboardAdmin               from '../hrms/DashboardAdmin.vue';
import HrmsAssessments                  from '../hrms/Assessments.vue';
import HrmsAssessmentsAdmin             from '../hrms/AssessmentsAdmin.vue';
import HrmsAssessmentsTeam              from '../hrms/AssessmentsTeam.vue';
import HrmsAssessmentHrItems            from '../hrms/AssessmentHrItems.vue';
import HrmsAssessmentPeriod             from '../hrms/AssessmentPeriod.vue';
import HrmsAssessmentPeriods            from '../hrms/AssessmentPeriods.vue';
import HrmsAttendanceSummaries          from '../hrms/AttendanceSummaries.vue';
import HrmsAttendanceSummary            from '../hrms/AttendanceSummary.vue';
import HrmsClockIns                     from '../hrms/ClockIns.vue';
import HrmsDepartments                  from '../hrms/Departments.vue'; 
import HrmsDesignations                 from '../hrms/Designations.vue';
import HrmsDesignation                  from '../hrms/Designation.vue';
import HrmsEducations                   from '../hrms/Educations.vue';
import HrmsEmployee                     from '../hrms/Employee.vue';
import HrmsEmployeeBonus                from '../hrms/EmployeeBonus.vue';
import HrmsEmployeeBonuses              from '../hrms/EmployeeBonuses.vue';
import HrmsEmployeeContact              from '../hrms/EmployeeContact.vue';
import HrmsEmployeeDeduction            from '../hrms/EmployeeDeduction.vue';
import HrmsEmployeeDeductions           from '../hrms/EmployeeDeductions.vue';
import HrmsEmployees                    from '../hrms/Employees.vue';
import HrmsEmployeeLeaveType            from '../hrms/EmployeeLeaveType.vue';
import HrmsEmployeeLeaveTypes           from '../hrms/EmployeeLeaveTypes.vue';
import HrmsJob                          from '../hrms/Job.vue';
import HrmsJobs                         from '../hrms/Jobs.vue';
import HrmsLeaveAllowanceMine           from '../hrms/LeaveAllowanceMine.vue';
import HrmsLeaveAllowances              from '../hrms/LeaveAllowances.vue';
import HrmsLeaveRequest                 from '../hrms/LeaveRequest.vue';
import HrmsLeaveRequests                from '../hrms/LeaveRequests.vue';
import HrmsLeaveRequestsAdmin           from '../hrms/LeaveRequestsAdmin.vue';
import HrmsLeaveRequestsTeam            from '../hrms/LeaveRequestsTeam.vue';
import HrmsLeaveType                    from '../hrms/LeaveType.vue';
import HrmsLeaveTypes                   from '../hrms/LeaveTypes.vue';
import HrmsLeaveUserLeaveTypes          from '../hrms/EmployeeLeaveTypes.vue';
import HrmsPayslips                     from '../hrms/Payslips.vue';
import HrmsTrainings                    from '../hrms/Trainings.vue';

    import HrmsDetailAccountList                from '../hrms/details/AccountList.vue'; 
    import HrmsDetailAssessmentPeriod           from '../hrms/details/AssessmentPeriod.vue'; 
    import HrmsDetailAssessmentHrItemList       from '../hrms/details/AssessmentHrItemList.vue';
    import HrmsDetailAssessmentList             from '../hrms/details/AssessmentList.vue';
    import HrmsDetailAttendanceSummary          from '../hrms/details/AttendanceSummary.vue';    
    import HrmsDetailAttendanceSummaryList      from '../hrms/details/AttendanceSummaryList.vue';
    import HrmsDetailClockInList                from '../hrms/details/ClockInList.vue';
    import HrmsDetailDesignation                from '../hrms/details/Designation.vue';
    import HrmsDetailDesignationKpiList         from '../hrms/details/DesignationKpiList.vue';
    import HrmsDetailEducation                  from '../hrms/details/Education.vue';
    import HrmsDetailEducationList              from '../hrms/details/EducationList.vue';
    import HrmsDetailEmployee                   from '../hrms/details/Employee.vue';
    import HrmsDetailEmployeeBonus              from '../hrms/details/EmployeeBonus.vue';
    import HrmsDetailEmployeeBonusList          from '../hrms/details/EmployeeBonusList.vue';
    import HrmsDetailEmployeeCard               from '../hrms/details/EmployeeCard.vue';
    import HrmsDetailEmployeeDeduction          from '../hrms/details/EmployeeDeduction.vue';
    import HrmsDetailEmployeeDeductionList      from '../hrms/details/EmployeeDeductionList.vue';
    import HrmsDetailEmployeeLeaveType          from '../hrms/details/EmployeeLeaveType.vue';
    import HrmsDetailEmployeeLeaveTypeList      from '../hrms/details/EmployeeLeaveTypeList.vue';
    import HrmsDetailEmployeeLeaveTypes         from '../hrms/details/EmployeeLeaveTypes.vue';
    import HrmsDetailEmployeeList               from '../hrms/details/EmployeeList.vue';
    import HrmsDetailJobList                    from '../hrms/details/JobList.vue';
    import HrmsDetailLeaveRequest               from '../hrms/details/LeaveRequest.vue';
    import HrmsDetailLeaveRequestList           from '../hrms/details/LeaveRequestList.vue';
    import HrmsDetailLeaveAllowanceList         from '../hrms/details/LeaveAllowanceList.vue';
    import HrmsDetailPayslipList                from '../hrms/details/PayslipList.vue';
    import HrmsDetailTraining                   from '../hrms/details/Training.vue';
    import HrmsDetailTrainingList               from '../hrms/details/TrainingList.vue';         

    import HrmsFormAssessmentHrItem             from '../hrms/forms/AssessmentHrItem.vue';
    import HrmsFormAssessmentPeriod             from '../hrms/forms/AssessmentPeriod.vue';
    import HrmsFormAttendanceSummary            from '../hrms/forms/AttendanceSummary.vue';
    import HrmsFormDesignation                  from '../hrms/forms/Designation.vue';
    import HrmsFormEducation                    from '../hrms/forms/Education.vue';
    import HrmsFormEmployee                     from '../hrms/forms/Employee.vue';
    import HrmsFormEmployeeAssignManager        from '../hrms/forms/EmployeeAssignManager.vue';
    import HrmsFormEmployeeBonus                from '../hrms/forms/EmployeeBonus.vue';
    import HrmsFormEmployeeDeduction            from '../hrms/forms/EmployeeDeduction.vue';
    import HrmsFormEmployeeImport               from '../hrms/forms/EmployeeImport.vue';
    import HrmsFormEmployeeLeaveType            from '../hrms/forms/EmployeeLeaveType.vue';
    import HrmsFormEmployeeStatus               from '../hrms/forms/EmployeeStatus.vue';
    import HrmsFormLeaveAllowance               from '../hrms/forms/LeaveAllowance.vue';
    import HrmsFormLeaveAllowanceConfirm        from '../hrms/forms/LeaveAllowanceConfirm.vue';
    import HrmsFormLeaveRequestImport           from '../hrms/forms/LeaveRequestImport.vue';
    import HrmsFormLeaveRequest                 from '../hrms/forms/LeaveRequest.vue';
    import HrmsFormLeaveRequestConfirm          from '../hrms/forms/LeaveRequestConfirm.vue';
    import HrmsFormLeaveType                    from '../hrms/forms/LeaveType.vue';
    import HrmsFormLeaveTypeEmployees           from '../hrms/forms/LeaveTypeEmployees.vue';


import InsuranceDashboard           from '../emr/insurance/Dashboard.vue';

import InsuranceClaims              from '../emr/insurance/Claims.vue';
import InsurancePlan                from '../emr/insurance/Plan.vue';  
import InsurancePlans               from '../emr/insurance/Plans.vue';  
import InsuranceProviders           from '../emr/insurance/Providers.vue';
import InsuranceProvidersSuspended  from '../emr/insurance/ProvidersSuspended.vue';
import InsuranceProvider            from '../emr/insurance/Provider.vue'; 
import InsuranceQueueAuthorizations from '../emr/insurance/QueueAuthorizations.vue';  
import InsuranceQueueCoPaid         from '../emr/insurance/QueueCoPaid.vue';  
import InsuranceQueueUncovered      from '../emr/insurance/QueueUncovered.vue';  

    import InsuranceDetailProviderContacts      from '../emr/insurance/details/ProviderContacts.vue';
    import InsuranceDetailProviderPlans         from '../emr/insurance/details/ProviderPlans.vue';
    import InsuranceDetailSummaryPlan           from '../emr/insurance/details/SummaryPlan.vue';
    import InsuranceDetailTransaction           from '../emr/insurance/details/Transaction.vue';
    import InsuranceDetailTransactionList       from '../emr/insurance/details/TransactionList.vue';

    import InsuranceFormAuthCode                from '../emr/insurance/forms/AuthCode.vue';
    import InsuranceFormAuthRequest             from '../emr/insurance/forms/AuthRequest.vue';
    import InsuranceFormContact                 from '../emr/insurance/forms/Contact.vue';
    import InsuranceFormPlan                    from '../emr/insurance/forms/Plan.vue';
    import InsuranceFormPlanBranch              from '../emr/insurance/forms/PlanBranch.vue';
    import InsuranceFormProvider                from '../emr/insurance/forms/Provider.vue';


import InventoryBrands              from '../inventory/Brands.vue';
import InventoryCategories          from '../inventory/Categories.vue';
import InventoryCategory            from '../inventory/Category.vue';
import InventoryClassification      from '../inventory/Classification.vue';
import InventoryClassifications     from '../inventory/Classifications.vue';
import InventoryDashboard           from '../inventory/Dashboard.vue';
import InventoryDirectPurchase      from '../inventory/DirectPurchase.vue';
import InventoryDirectPurchases     from '../inventory/DirectPurchases.vue';
import InventoryItem                from '../inventory/Item.vue';
import InventoryItems               from '../inventory/Items.vue';
import InventoryItemsBulk           from '../inventory/ItemsBulk.vue';
import InventoryItemTypes           from '../inventory/ItemTypes.vue';
import InventoryPackage             from '../inventory/Package.vue';
import InventoryPackages            from '../inventory/Packages.vue';
import InventorySalesOrder          from '../inventory/SalesOrder.vue';
import InventorySalesOrders         from '../inventory/SalesOrders.vue';
import InventoryStore               from '../inventory/Store.vue';
import InventoryStores              from '../inventory/Stores.vue';
import InventoryTransferOrder       from '../inventory/TransferOrder.vue';
import InventoryTransferOrdersIn    from '../inventory/TransferOrdersIn.vue';
import InventoryTransferOrdersOut   from '../inventory/TransferOrdersOut.vue';
import InventoryUserStore           from '../inventory/UserStore.vue';

    import InventoryDetailBrand              from '../inventory/details/Brand.vue';    
    import InventoryDetailItem                  from '../inventory/details/Item.vue'; 
    import InventoryDetailItemList              from '../inventory/details/ItemList.vue'; 
    import InventoryDetailStoreExpired          from '../inventory/details/StoreExpired.vue'; 
    import InventoryDetailStoreItem             from '../inventory/details/StoreItem.vue'; 
    import InventoryDetailStoreItemList         from '../inventory/details/StoreItemList.vue'; 
    import InventoryDetailStoreSoonToExpire     from '../inventory/details/StoreSoonToExpire.vue';
    import InventoryDetailStoreSummary          from '../inventory/details/StoreSummary.vue';
    import InventoryDetailTransferOrder         from '../inventory/details/TransferOrder.vue'; 
    import InventoryDetailTransferOrderList     from '../inventory/details/TransferOrderList.vue'; 
    
    import InventoryFormBrand                   from '../inventory/forms/Brand.vue';
    import InventoryFormCategory                from '../inventory/forms/Category.vue';
    import InventoryFormClassification          from '../inventory/forms/Classification.vue';
    import InventoryFormFulfill                 from '../inventory/forms/Fulfill.vue';
    import InventoryFormFulfillment             from '../inventory/forms/Fulfillment.vue';
    import InventoryFormItem                    from '../inventory/forms/Item.vue';
    import InventoryFormItemBulk                from '../inventory/forms/ItemBulk.vue';
    import InventoryFormItemImport              from '../inventory/forms/ItemImport.vue';
    import InventoryFormItemSearch              from '../inventory/forms/ItemSearch.vue';
    import InventoryFormItemType                from '../inventory/forms/ItemType.vue';
    import InventoryFormSalesOrder              from '../inventory/forms/SalesOrder.vue';
    import InventoryFormStore                   from '../inventory/forms/Store.vue';
    import InventoryFormStoreIssue              from '../inventory/forms/StoreIssue.vue';
    import InventoryFormStoreItemSetting        from '../inventory/forms/StoreItemSetting.vue';
    import InventoryFormTransferOrder           from '../inventory/forms/TransferOrder.vue';
    import InventoryFormTransferOrderReject     from '../inventory/forms/TransferOrderReject.vue';


import LearnCategories  from '../learn/Categories.vue';
import LearnCertificates  from '../learn/Certificates.vue';
import LearnSubCategory from '../learn/SubCategory.vue'; 

import LearnAdminCourse                 from '../learn/AdminCourse.vue';
import LearnAdminCourses                from '../learn/AdminCourses.vue';
import LearnAdminDashboard              from '../learn/AdminDashboard.vue';
import LearnAdminExam                   from '../learn/AdminExam.vue';
import LearnAdminExams                  from '../learn/AdminExams.vue';
import LearnAdminExamResult             from '../learn/AdminExamResult.vue';
import LearnAdminExamResults            from '../learn/AdminExamResults.vue';
import LearnAdminLesson                 from '../learn/AdminLesson.vue';
import LearnAdminLessons                from '../learn/AdminLessons.vue';
import LearnAdminUserCourse             from '../learn/AdminUserCourse.vue';
import LearnAdminUserCourses            from '../learn/AdminUserCourses.vue';
import LearnCourse                      from '../learn/Course.vue';
import LearnCourses                     from '../learn/Courses.vue';
import LearnDashboard                   from '../learn/Dashboard.vue';
import LearnExams                       from '../learn/Exams.vue';
import LearnLesson                      from '../learn/Lesson.vue';
import LearnLessons                     from '../learn/Lessons.vue';
import LearnResult                      from '../learn/ExamResult.vue';
import LearnStudentResults              from '../learn/ExamResults.vue';
import LearnTutorCourse                 from '../learn/TutorCourse.vue';
import LearnTutorCourses                from '../learn/TutorCourses.vue';
import LearnTutorExams                  from '../learn/TutorExams.vue';
import LearnTutorLesson                 from '../learn/TutorLesson.vue';
import LearnTutorLessons                from '../learn/TutorLessons.vue';
import LearnTutorExamResults            from '../learn/TutorExamResults.vue';
import LearnUserCourse                  from '../learn/UserCourse.vue';
import LearnUserCourses                 from '../learn/UserCourses.vue';
import LearnUserExam                    from '../learn/UserExam.vue';
import LearnUserExams                   from '../learn/UserExams.vue';

    import LearnDetailAssignTutor           from '../learn/details/AssignTutor.vue';
    import LearnDetailAssignUser            from '../learn/details/AssignUser.vue';
    import LearnDetailCategory              from '../learn/details/Category.vue';
    import LearnDetailCourse                from '../learn/details/Course.vue';
    import LearnDetailCourseList            from '../learn/details/CourseList.vue';
    import LearnDetailExam                  from '../learn/details/Exam.vue';
    import LearnDetailExamList              from '../learn/details/ExamList.vue';
    import LearnDetailExamResult            from '../learn/details/ExamResult.vue';
    import LearnDetailExamResultList        from '../learn/details/ExamResultList.vue';
    import LearnDetailLesson                from '../learn/details/Lesson.vue';
    import LearnDetailLessonList            from '../learn/details/LessonList.vue';
    import LearnDetailOptionList            from '../learn/details/OptionList.vue';
    import LearnDetailQuestion              from '../learn/details/Question.vue';
    import LearnDetailQuestionList          from '../learn/details/QuestionList.vue';
    import LearnDetailSubCategory           from '../learn/details/SubCategory.vue';
    import LearnDetailUserCourse            from '../learn/details/UserCourse.vue';
    import LearnDetailUserCourseList        from '../learn/details/UserCourseList.vue';
    import LearnDetailUserExam              from '../learn/details/UserExam.vue';
    import LearnDetailUserExamList          from '../learn/details/UserExamList.vue';
    
    import LearnFormAssignTutor   from '../learn/forms/AssignTutor.vue';
    import LearnFormAssignUser    from '../learn/forms/AssignUser.vue';
    import LearnFormCategory      from '../learn/forms/Category.vue';
    import LearnFormCourse        from '../learn/forms/Course.vue';
    import LearnFormExam          from '../learn/forms/Exam.vue';
    import LearnFormLesson        from '../learn/forms/Lesson.vue';
    import LearnFormLessons       from '../learn/forms/Lessons.vue';
    import LearnFormOption        from '../learn/forms/Option.vue';
    import LearnFormQuestion      from '../learn/forms/Question.vue';
    import LearnFormSubCategory   from '../learn/forms/SubCategory.vue';


import LoanDashboard                from '../loans/Dashboard.vue';

import NoticeAdmin                  from '../notices/Admin.vue';
import NoticeAll                    from '../notices/All.vue';
import NoticeBoard                  from '../notices/Board.vue';
import NoticeSingle                 from '../notices/Single.vue';
        
    import NoticeDetailList             from '../notices/details/List.vue';    

    import NoticeForm    from '../notices/forms/New.vue';

import OperationBranch                  from '../operations/Branch.vue';
import OperationBranches                from '../operations/Branches.vue';
import OperationDashboard               from '../operations/Dashboard.vue';
import OperationDepartment              from '../operations/Department.vue';
import OperationDepartments             from '../operations/Departments.vue';
import OperationPriceList               from '../operations/PriceList.vue';
import OperationPriceLists              from '../operations/PriceLists.vue';

    import OperationDetailBranch                from '../operations/details/Branch.vue';    
    import OperationDetailBranchList            from '../operations/details/BranchList.vue';
    import OperationDetailDepartmentList        from '../operations/details/DepartmentList.vue';
    import OperationDetailPricelistList         from '../operations/details/PricelistList.vue'; 
    import OperationDetailServiceList           from '../operations/details/ServiceList.vue'; 
    
    import OperationFormBranch                  from '../operations/forms/Branch.vue';
    import OperationFormDepartment              from '../operations/forms/Department.vue';
    import OperationFormService                 from '../operations/forms/Service.vue';
    import OperationFormServiceType             from '../operations/forms/ServiceType.vue';

import PoliciesAdmin                from '../policies/Admin.vue';
import PoliciesDepartmental         from '../policies/Departmental.vue';
import PoliciesGeneral              from '../policies/General.vue';
import PoliciesSingle               from '../policies/Single.vue';

    import PoliciesDetailList       from '../policies/details/List.vue';
    import PoliciesFormAssign       from '../policies/forms/Assign.vue';
    import PoliciesFormNew          from '../policies/forms/New.vue';

import ProcurementApprovalMatrices      from '../procurement/ApprovalMatrices.vue';
import ProcurementDashboard             from '../procurement/Dashboard.vue';
import ProcurementPaymentTerms          from '../procurement/PaymentTerms.vue';
import ProcurementPurchaseOrder         from '../procurement/PurchaseOrder.vue';
import ProcurementPurchaseOrderNew      from '../procurement/PurchaseOrderNew.vue';
import ProcurementPurchaseOrders        from '../procurement/PurchaseOrders.vue';
import ProcurementSettings              from '../procurement/Settings.vue';
import ProcurementVendor                from '../procurement/Vendor.vue';
import ProcurementVendorCategories      from '../procurement/VendorCategories.vue';
import ProcurementVendors               from '../procurement/Vendors.vue';
import ProcurementWorkOrder             from '../procurement/WorkOrder.vue';
import ProcurementWorkOrderNew          from '../procurement/WorkOrderNew.vue';
import ProcurementWorkOrders            from '../procurement/WorkOrders.vue';

    import ProcurementDetailApprovalTrail                   from '../procurement/details/ApprovalTrail.vue';    
    import ProcurementDetailApprovalMatrix                  from '../procurement/details/ApprovalMatrix.vue';
    import ProcurementDetailBatch                           from '../procurement/details/Batch.vue';
    import ProcurementDetailBatchList                       from '../procurement/details/BatchList.vue';
    import ProcurementDetailPurchaseOrder                   from '../procurement/details/PurchaseOrder.vue';
    import ProcurementDetailPurchaseOrderApprovalList       from '../procurement/details/PurchaseOrderApprovalList.vue';
    import ProcurementDetailPurchaseOrderItemList           from '../procurement/details/PurchaseOrderItemList.vue';
    import ProcurementDetailPurchaseOrderList               from '../procurement/details/PurchaseOrderList.vue';
    import ProcurementDetailPurchaseOrderSummary            from '../procurement/details/PurchaseOrderSummary.vue';
    import ProcurementDetailStatusRibbon                    from '../procurement/details/StatusRibbon.vue';
    import ProcurementDetailVendorContactList               from '../procurement/details/VendorContactList.vue';
    import ProcurementDetailWorkOrder                       from '../procurement/details/WorkOrder.vue';
    import ProcurementDetailWorkOrderList                   from '../procurement/details/WorkOrderList.vue';

    import ProcurementFormAdditionalCost                    from '../procurement/forms/AdditionalCost.vue';
    import ProcurementFormApprovalMatrix                    from '../procurement/forms/ApprovalMatrix.vue';
    import ProcurementFormAssignStore                       from '../procurement/forms/AssignStore.vue';
    import ProcurementFormAssignVendor                      from '../procurement/forms/AssignVendor.vue';
    import ProcurementFormBatch                             from '../procurement/forms/Batch.vue';
    import ProcurementFormBatchApproval                     from '../procurement/forms/BatchApproval.vue';
    import ProcurementFormOtherCost                         from '../procurement/forms/OtherCost.vue';
    import ProcurementFormPaymentTerm                       from '../procurement/forms/PaymentTerm.vue';
    import ProcurementFormPurchaseOrder                     from '../procurement/forms/PurchaseOrder.vue';
    import ProcurementFormPurchaseOrderApproval             from '../procurement/forms/PurchaseOrderApproval.vue';
    import ProcurementFormPurchaseOrderItem                 from '../procurement/forms/PurchaseOrderItem.vue';
    //import ProcurementFormPurchaseOrderItemList             from '../procurement/forms/PurchaseOrderItemList.vue'; 
    import ProcurementFormVendor                            from '../procurement/forms/Vendor.vue';
    import ProcurementFormVendorCategory                    from '../procurement/forms/VendorCategory.vue';
    import ProcurementFormVendorContact                     from '../procurement/forms/VendorContact.vue';
    import ProcurementFormWorkOrder                         from '../procurement/forms/WorkOrder.vue';
    import ProcurementFormWorkOrderItem                     from '../procurement/forms/WorkOrderItem.vue';

import SalesCustomer                from '../sales_orders/Customer.vue';
import SalesCustomers               from '../sales_orders/Customers.vue';
import SalesDashboard               from '../sales_orders/Dashboard.vue';
import SalesDeliveryNote            from '../sales_orders/DeliveryNote.vue';
import SalesDeliveryNotes           from '../sales_orders/DeliveryNotes.vue';
import SalesInvoice                 from '../sales_orders/Quotation.vue';
import SalesInvoices                from '../sales_orders/Invoices.vue';
import SalesOrder                   from '../sales_orders/Order.vue';
import SalesOrderFulfill            from '../sales_orders/OrderFulfill.vue';
import SalesOrders                  from '../sales_orders/Orders.vue';
import SalesReports                 from '../sales_orders/Reports.vue';
import SalesReturn                  from '../sales_orders/Return.vue';
import SalesReturns                 from '../sales_orders/Returns.vue';
import SalesQuotation               from '../sales_orders/Quotation.vue';
import SalesQuotations              from '../sales_orders/Quotations.vue';

    import SalesDetailDeliveryNote                      from '../sales_orders/details/DeliveryNote.vue';
    import SalesDetailDeliveryNoteList                  from '../sales_orders/details/DeliveryNoteList.vue';
    import SalesDetailOrder                             from '../sales_orders/details/Order.vue';
    import SalesDetailOrderList                         from '../sales_orders/details/OrderList.vue';
    import SalesDetailOrderItemList                     from '../sales_orders/details/OrderItemList.vue';
    import SalesDetailOrderSummary                      from '../sales_orders/details/OrderSummary.vue';
    import SalesDetailReceipt                           from '../sales_orders/details/Receipt.vue';
    import SalesDetailReturn                            from '../sales_orders/details/Return.vue';
    import SalesDetailReturnList                        from '../sales_orders/details/ReturnList.vue';
    import SalesDetailQuotation                         from '../sales_orders/details/Quotation.vue';
    import SalesDetailQuotationList                     from '../sales_orders/details/QuotationList.vue';

    import SalesDetailReportDailySales                  from '../sales_orders/details/ReportDailySales.vue';
    import SalesDetailReportSalesItemDetailed           from '../sales_orders/details/ReportSalesItemDetailed.vue';

    import SalesFormDeliveryNote                        from '../sales_orders/forms/DeliveryNote.vue';
    import SalesFormFulfill                             from '../sales_orders/forms/Fulfill.vue';
    import SalesFormFulfillOrderItem                    from '../sales_orders/forms/FulfillOrderItem.vue';
    import SalesFormOrder                               from '../sales_orders/forms/Order.vue';
    import SalesFormQuotation                           from '../sales_orders/forms/Quotation.vue';
    import SalesFormReturn                              from '../sales_orders/forms/Return.vue';
    //import SalesFormSales                               from '../sales_orders/forms/Sales.vue';
    
import SOMAdmin             from '../som/Admin.vue';
import SOMCloseNominations  from '../som/CloseNominations.vue';
import SOMDetail            from '../som/Detail.vue';

import SOMWinners           from '../som/Winners.vue';

    import SOMDetailNominations     from '../som/details/Nominations.vue';  
    import SOMDetailVotes           from '../som/details/Votes.vue';    
    import SOMDetailWinners         from '../som/details/Winners.vue';
    import SOMFormMonth             from '../som/forms/Month.vue';

import TicketAdmin              from '../ticketing/Admin.vue';

import TicketDepartment         from '../ticketing/Department.vue';
import TicketPersonal           from '../ticketing/Personal.vue';
import TicketSetting            from '../ticketing/Setting.vue';
import TicketSingle             from '../ticketing/Single.vue';

    import TicketDetailDashboard        from '../ticketing/details/Dashboard.vue';
    import TicketDetailList             from '../ticketing/details/List.vue';

    import TicketFormAssign     from '../ticketing/forms/Assign.vue';
    import TicketFormAccept     from '../ticketing/forms/Accept.vue';
    import TicketFormComplete   from '../ticketing/forms/Complete.vue';
    import TicketFormNew        from '../ticketing/forms/New.vue';
    import TicketFormReply      from '../ticketing/forms/Reply.vue';

import UmsBirthday              from '../users/Birthday.vue';
import UmsBirthdays             from '../users/Birthdays.vue';
import UmsCompleteRegistration  from '../users/CompleteRegistration.vue';
import UmsContacts              from '../users/Contacts.vue';
import UmsContact               from '../users/Contact.vue';
import UmsProfile               from '../users/Profile.vue';
import UmsRole                  from '../users/Role.vue';
import UmsRoles                 from '../users/Roles.vue';
import UmsStaffs                from '../users/Staffs.vue';
import UmsStaffsLatest          from '../users/StaffsLatest.vue';
import UmsUsers                 from '../users/Users.vue';


    import UmsDetailBioData                 from '../users/details/BioData.vue';
    import UmsDetailUserlist                from '../users/details/Userlist.vue';

    import UmsFormAssignRole                from '../users/forms/AssignRole.vue';
    import UmsFormBioData                   from '../users/forms/BioData.vue';
    import UmsFormNOK                       from '../users/forms/NextOfKin.vue';
    import UmsFormPassword                  from '../users/forms/Password.vue';
    import UmsFormRole                      from '../users/forms/Role.vue';
    import UmsFormStaff                     from '../users/forms/Staff.vue';    

import Error404 from '../general/errors/404.vue';
import component from 'vue3-paystack';

const routes = [
    {path: '/',                                                 component: DashboardMain},

    {path: '/approvals',                                        component: ApprovalDashboard},
    {path: '/approvals/batches',                                component: ApprovalBatches},
    {path: '/approvals/dashboard',                              component: ApprovalDashboard},
    //{path: '/approvals/categories',                              component: ApprovalCategories},
    //{path: '/approvals/categories/:id',                          component: ApprovalCategory},
    {path: '/approvals/documents',                              component: ApprovalDocuments},
    {path: '/approvals/expenses',                               component: ApprovalExpenses},
    {path: '/approvals/invoices',                               component: ApprovalInvoices},
    {path: '/approvals/job_completions',                        component: ApprovalJobCompletions},
    {path: '/approvals/returns',                                component: ApprovalOrderReturns},
    {path: '/approvals/returns/:id',                            component: ApprovalOrderReturn},
    {path: '/approvals/purchase_orders',                        component: ApprovalPurchaseOrders},
    //{path: '/approvals/purchase_orders/:id',                     component: ApprovalPurchaseOrder},
    {path: '/approvals/purchase_requests',                      component: ApprovalPurchaseRequests},
    //{path: '/approvals/purchase_requests/:id',                   component: ApprovalPurchaseRequest},
    {path: '/approvals/sales_orders',                           component: ApprovalSalesOrders},
    {path: '/approvals/sales_orders/:id',                       component: ApprovalSalesOrder},
    
    {path: '/archives',                                         component: ArchiveDashboard},
    {path: '/archives/dashboard',                               component: ArchiveDashboard},
    {path: '/archives/categories',                              component: ArchiveCategories},
    {path: '/archives/categories/:id',                          component: ArchiveCategory},
    {path: '/archives/documents',                               component: ArchiveDocuments},
    {path: '/archives/documents/:id',                           component: ArchiveDocument},

    {path: '/chats/dashboard',                                  component: ChatDashboard},
    {path: '/chats/rooms',                                      component: ChatRooms},
    {path: '/chats/rooms/:id',                                  component: ChatRoom},

    {path: '/customer_relations',                               component: CRMDashboard},
    {path: '/customer_relations/customers',                     component: CRMCustomers},
    {path: '/customer_relations/customers/:id',                 component: CRMCustomer},
    {path: '/customer_relations/dashboard',                     component: CRMDashboard},
    {path: '/customer_relations/leads',                         component: CRMLeads},
    {path: '/customer_relations/leads/:id',                     component: CRMLead},
    
    {path: '/contacts',                                         component: UmsContacts},
    {path: '/contacts/:id',                                     component: UmsContact},
    {path: '/dashboard',                                        component: DashboardMain},

    {path: '/emr/consultations',                                component: EMRConsultantDashboard},
    {path: '/emr/consultations/dashboard',                      component: EMRConsultantDashboard},
    {path: '/emr/consultations/detailed/:id',                   component: EMRConsultantDetailConsultation},
    {path: '/emr/consultations/department_queue/:id',           component: EMRConsultantQueueDepartment},
    {path: '/emr/consultations/doctor_queue',                   component: EMRConsultantQueueDoctor},
    {path: '/emr/consultations/my_previous_consultations',      component: EMRConsultantMyPastConsultations},
    {path: '/emr/consultations/my_queue',                       component: EMRConsultantQueueMy},
    {path: '/emr/consultations/start/:id',                      component: EMRConsultantConsultation},
    
    {path: '/emr/front_office',                                 component: EMRFrontOfficeDashboard},
    {path: '/emr/front_office/appointments',                    component: EMRFrontOfficeAppointments},
    {path: '/emr/front_office/appointments/:id',                component: EMRFrontOfficeAppointment},
    {path: '/emr/front_office/dashboard',                       component: EMRFrontOfficeDashboard},
    {path: '/emr/front_office/patients',                        component: EMRPatientAll},
    {path: '/emr/front_office/patients/new',                    component: EMRPatientFormRegistration},
    {path: '/emr/front_office/patients/search',                 component: EMRPatientSearch},
    {path: '/emr/front_office/patients/:id',                    component: EMRPatientSingle},

    {path: '/emr/insurance',                                    component: InsuranceDashboard},
    {path: '/emr/insurance/dashboard',                          component: InsuranceDashboard},
    
    {path: '/emr/insurance/claims',                             component: InsuranceClaims},

    {path: '/emr/insurance/plans',                              component: InsurancePlans},
    {path: '/emr/insurance/plans/:id',                          component: InsurancePlan},
    {path: '/emr/insurance/providers',                          component: InsuranceProviders},
    {path: '/emr/insurance/providers/suspended',                component: InsuranceProvidersSuspended},
    {path: '/emr/insurance/providers/:id',                      component: InsuranceProvider},

    {path: '/emr/insurance/queue',                              component: InsuranceQueueAuthorizations},
    {path: '/emr/insurance/queue/authorizations',               component: InsuranceQueueAuthorizations},
    {path: '/emr/insurance/queue/co-paid',                      component: InsuranceQueueCoPaid},
    {path: '/emr/insurance/queue/uncovered',                    component: InsuranceQueueUncovered},

    {path: '/emr/nursing/dashboard',                            component: EMRNursingDashboard},
    
    {path: '/emr/operations',                                   component: EMROperationsDashboard},
    {path: '/emr/operations/branches',                          component: EMROperationsBranches},
    {path: '/emr/operations/branches/:id',                      component: EMROperationsBranch},
    {path: '/emr/operations/branch_price_lists/:id',            component: EMROperationsBranchPriceList},
    {path: '/emr/operations/dashboard',                         component: EMROperationsDashboard},
    {path: '/emr/operations/departments',                       component: EMROperationsDepartments},
    {path: '/emr/operations/price_lists',                       component: EMROperationsPricelists},
    {path: '/emr/operations/price_lists/:id',                   component: EMROperationsPricelist},
    {path: '/emr/operations/services',                          component: EMROperationsServices},
    {path: '/emr/operations/services/:id',                      component: EMROperationsService},
    {path: '/emr/operations/service_types',                     component: EMROperationsServiceTypes},

    {path: '/emr/radiology',                                    component: EMRRadiologyDashboard},
    {path: '/emr/radiology/insurance',                          component: EMRRadiologyInsurance},
    {path: '/emr/radiology/queues',                             component: EMRRadiologyQueue},
    {path: '/emr/radiology/referred_in',                        component: EMRRadiologyReferredIn},
    {path: '/emr/radiology/referred_out',                       component: EMRRadiologyReferredOut},

    {path: '/equipments',                                       component: EquipmentDashboard},
    {path: '/equipments/assets/items',                          component: EquipmentAssets},
    {path: '/equipments/assets/item',                           component: EquipmentAsset},
    
    {path: '/escrows',                                          component: EscrowDashboard},
    {path: '/escrows/dashboard',                                component: EscrowDashboard},
    {path: '/escrows/disputes',                                 component: EscrowDisputes},
    {path: '/escrows/disputes/:id',                             component: EscrowDispute},
    {path: '/escrows/products',                                 component: EscrowProducts},
    {path: '/escrows/products/:id',                             component: EscrowProduct},
    {path: '/escrows/transactions',                             component: EscrowTransactions},
    {path: '/escrows/transactions/:id',                         component: EscrowTransaction},

    {path: '/escrow_admin',                                     component: EscrowAdminDashboard},
    {path: '/escrow_admin/dashboard',                           component: EscrowAdminDashboard},
    {path: '/escrow_admin/disputes',                            component: EscrowAdminDisputes},
    {path: '/escrow_admin/disputes/:id',                        component: EscrowAdminDispute},
    {path: '/escrow_admin/products',                            component: EscrowAdminProducts},
    {path: '/escrow_admin/products/:id',                        component: EscrowAdminProduct},
    {path: '/escrow_admin/transactions',                        component: EscrowAdminTransactions},
    {path: '/escrow_admin/transactions/:id',                    component: EscrowAdminTransaction},
    {path: '/escrow_admin/users',                               component: UmsUsers},
    
    {path: '/facility',                                         component: FacilityDashboard},
    {path: '/facility',                                         component: FacilityDashboard},
    {path: '/facility',                                         component: FacilityDashboard},
    {path: '/facility',                                         component: FacilityDashboard},

    {path: '/finance',                                          component: FinanceDashboard},
    {path: '/finance/assets',                                   component: FinanceAssets},
    {path: '/finance/assets/:id',                               component: FinanceAsset},
    {path: '/finance/dashboard',                                component: FinanceDashboard},
    {path: '/finance/cash_register',                            component: FinanceCashRegister},
    {path: '/finance/deposits',                                 component: FinanceDeposits},
    {path: '/finance/expenses',                                 component: FinanceExpenses},
    {path: '/finance/expenses/:id',                             component: FinanceExpense},
    {path: '/finance/incomes',                                  component: FinanceIncomes},
    {path: '/finance/incomes/:id',                              component: FinanceIncome},
    {path: '/finance/invoices',                                 component: FinanceInvoices},
    {path: '/finance/invoices/:id',                             component: FinanceInvoice},
    {path: '/finance/payments',                                 component: FinancePayments},
    {path: '/finance/pay_outs',                                 component: FinancePayOuts},
    {path: '/finance/pay_outs/:id',                             component: FinancePayOut},
    {path: '/finance/reports',                                  component: FinanceReports},
    {path: '/finance/transactions',                             component: FinanceTransactions},
    {path: '/finance/transactions/:id',                         component: FinanceTransaction},
    {path: '/finance/visit/:id',                                component: FinanceVisit},
    
    {path: '/finance/settings/branch_accounts',                 component: FinanceBranchAccounts}, 
    {path: '/finance/settings/branch_accounts/:id',             component: FinanceDetailBranchAccount},
    {path: '/finance/settings/branch_price_lists',              component: FinanceBranchPriceLists},   
    {path: '/finance/settings/branch_price_lists/:id',          component: FinanceBranchPriceList}, 
    {path: '/finance/settings/expense_types',                   component: FinanceExpenseTypes}, 
    {path: '/finance/settings/payment_modes',                   component: FinancePaymentModes},
    {path: '/finance/settings/price_lists',                     component: FinancePricelists}, 
    {path: '/finance/settings/price_lists/:id',                 component: FinancePricelist}, 

    {path: '/home',                                             component: DashboardMain},
    
    //HRMS
    {path: '/hrms_admin/assessments',                           component: HrmsAssessments},
    {path: '/hrms_admin/assessment_hr_items',                   component: HrmsAssessmentHrItems},
    {path: '/hrms_admin/assessment_periods',                    component: HrmsAssessmentPeriods},
    {path: '/hrms_admin/dashboard',                             component: HrmsDashboardAdmin},
    {path: '/hrms_admin/departments',                           component: HrmsDepartments},
    {path: '/hrms_admin/departments/:id',                       component: OperationDepartment},
    {path: '/hrms_admin/designations',                          component: HrmsDesignations},
    {path: '/hrms_admin/designations/:id',                      component: HrmsDesignation},
    {path: '/hrms_admin/employees',                             component: HrmsEmployees},
    {path: '/hrms_admin/employees/:id',                         component: HrmsEmployee},
    {path: '/hrms_admin/employee_bonuses',                      component: HrmsEmployeeBonuses},
    {path: '/hrms_admin/employee_bonuses/:id',                  component: HrmsEmployeeBonus},
    {path: '/hrms_admin/employee_deductions',                   component: HrmsEmployeeDeductions},
    {path: '/hrms_admin/employee_deductions/:id',               component: HrmsEmployeeDeductions},
    {path: '/hrms_admin/employees',                             component: HrmsEmployees},
    {path: '/hrms_admin/employees/:id',                         component: HrmsEmployee},
    {path: '/hrms_admin/jobs',                                  component: HrmsJobs},
    {path: '/hrms_admin/jobs/:id',                              component: HrmsJob},
    {path: '/hrms_admin/leave_requests',                        component: HrmsLeaveRequestsAdmin},
    {path: '/hrms_admin/leave_types',                           component: HrmsLeaveTypes},
    {path: '/hrms_admin/leave_types/:id',                       component: HrmsLeaveType},
    {path: '/hrms_admin/leave_allowances',                      component: HrmsLeaveAllowances},

    {path: '/hrms',                                             component: HrmsDashboard},
    {path: '/hrms/assessments',                                 component: HrmsAssessments},
    {path: '/hrms/assessments_team',                            component: HrmsAssessmentsTeam},
    {path: '/hrms/attendance_summaries',                        component: HrmsAttendanceSummaries},
    {path: '/hrms/clock_ins',                                   component: HrmsClockIns},
    {path: '/hrms/dashboard',                                   component: HrmsDashboard},
    {path: '/hrms/educations',                                  component: HrmsEducations},
    {path: '/hrms/leaves/allowances',                           component: HrmsLeaveAllowanceMine},
    {path: '/hrms/leaves/all_requests',                         component: HrmsLeaveRequestsAdmin},
    {path: '/hrms/leaves/requests',                             component: HrmsLeaveRequests},
    {path: '/hrms/leaves/team_requests',                        component: HrmsLeaveRequestsTeam},
    {path: '/hrms/leaves/types',                                component: HrmsLeaveTypes},
    {path: '/hrms/leaves/types_assigned',                       component: HrmsLeaveUserLeaveTypes},
    {path: '/hrms/payslips',                                    component: HrmsPayslips},
    {path: '/hrms/trainings',                                   component: HrmsTrainings},

    {path: '/inventory',                                    component: InventoryDashboard},
    {path: '/inventory/dashboard',                          component: InventoryDashboard},
    {path: '/inventory/direct_purchases',                   component: InventoryDirectPurchases},
    {path: '/inventory/direct_purchases/:id',               component: InventoryDirectPurchase},
    {path: '/inventory/items',                              component: InventoryItems},
    {path: '/inventory/items/:id',                          component: InventoryItem},
    {path: '/inventory/items_bulk',                         component: InventoryItemsBulk},
    {path: '/inventory/packages',                           component: InventoryPackages},
    {path: '/inventory/packages/:id',                       component: InventoryPackage},
    {path: '/inventory/sales_orders',                       component: InventorySalesOrders},
    {path: '/inventory/sales_orders/:id',                   component: InventorySalesOrder},
    {path: '/inventory/stores',                             component: InventoryStores},
    {path: '/inventory/stores/:id',                         component: InventoryStore},
    {path: '/inventory/transfer_orders/in',                 component: InventoryTransferOrdersIn},
    {path: '/inventory/transfer_orders/out',                component: InventoryTransferOrdersOut},
    {path: '/inventory/transfer_orders/:id',                component: InventoryTransferOrder},
    {path: '/inventory/user_stores/:id',                    component: InventoryUserStore},

    {path: '/inventory/settings/brands',                    component: InventoryBrands},
    {path: '/inventory/settings/categories',                component: InventoryCategories},
    {path: '/inventory/settings/categories/:id',            component: InventoryCategory},
    {path: '/inventory/settings/classifications',           component: InventoryClassifications},
    {path: '/inventory/settings/classifications/:id',       component: InventoryClassification},
    {path: '/inventory/settings/item_types',                component: InventoryItemTypes},

    {path: '/learn/admin',                                  component: LearnAdminDashboard},
    {path: '/learn/admin/dashboard',                        component: LearnAdminDashboard},
    {path: '/learn/admin/courses',                          component: LearnAdminCourses},
    {path: '/learn/admin/courses/:id',                      component: LearnCourse},
    {path: '/learn/admin/lessons',                          component: LearnLessons},
    {path: '/learn/admin/lessons/:id',                      component: LearnLesson},
    {path: '/learn/admin/user_courses',                     component: LearnAdminUserCourses},
    {path: '/learn/admin/user_courses/:id',                 component: LearnAdminUserCourse},

    {path: '/learn/student/dashboard',                      component: LearnDashboard},
    {path: '/learn/student/user_courses',                   component: LearnUserCourses},
    {path: '/learn/student/user_courses/:id',               component: LearnUserCourse},
    {path: '/learn/student/user_exams',                     component: LearnUserExams},
    {path: '/learn/student/user_exams/:id',                 component: LearnUserExam},

    {path: '/loans/dashboard',                              component: LoanDashboard},

    {path: '/operations',                                   component: OperationDashboard},
    {path: '/operations/branches',                          component: OperationBranches},
    {path: '/operations/branches/:id',                      component: OperationBranch},
    {path: '/operations/dashboard',                         component: OperationDashboard},
    {path: '/operations/departments',                       component: OperationDepartments},
    {path: '/operations/departments/:id',                   component: OperationDepartment},
    {path: '/operations/price_lists',                       component: OperationPriceLists},
    {path: '/operations/price_lists/:id',                   component: OperationPriceList},

    {path: '/policies',                                     component: PoliciesDepartmental},
    {path: '/policies/admin',                               component: PoliciesAdmin},
    {path: '/policies/departmental',                        component: PoliciesDepartmental},
    {path: '/policies/general',                             component: PoliciesGeneral},
    {path: '/policies/view/:id',                            component: PoliciesSingle},

    {path:'/procurement',                                   component: ProcurementDashboard},
    {path:'/procurement/dashboard',                         component: ProcurementDashboard},
    {path:'/procurement/items',                             component: InventoryItems},
    {path:'/procurement/purchase_orders',                   component: ProcurementPurchaseOrders},
    {path:'/procurement/purchase_orders/create',            component: ProcurementPurchaseOrderNew},
    {path:'/procurement/purchase_orders/:id',               component: ProcurementPurchaseOrder},
    {path:'/procurement/settings/approval_matrices',        component: ProcurementApprovalMatrices},
    {path:'/procurement/settings/general',                  component: ProcurementSettings},
    {path:'/procurement/settings/payment_terms',            component: ProcurementPaymentTerms},
    {path:'/procurement/settings/vendor_categories',        component: ProcurementVendorCategories},
    {path:'/procurement/vendors',                           component: ProcurementVendors},
    {path:'/procurement/vendors/:id',                       component: ProcurementVendor},
    {path:'/procurement/work_orders',                       component: ProcurementWorkOrders},
    {path:'/procurement/work_orders/create',                component: ProcurementWorkOrderNew},
    {path:'/procurement/work_orders/:id',                   component: ProcurementWorkOrder},

    {path:'/profile',                                       component: UmsProfile},

    //Notices
    {path: '/notices',                                      component: NoticeAll},
    {path: '/notices/admin',                                component: NoticeAdmin},
    {path: '/notices/:id',                                  component: NoticeSingle},

    {path: '/registration_complete/:id',                    component: UmsCompleteRegistration},

    //Sales Orders
    {path: '/sales_orders',                                  component: SalesDashboard},
    {path: '/sales_orders/customers',                        component: SalesCustomers},
    {path: '/sales_orders/customers/:id',                    component: SalesCustomer},
    {path: '/sales_orders/dashboard',                        component: SalesDashboard},
    {path: '/sales_orders/delivery_notes',                   component: SalesDeliveryNotes},
    {path: '/sales_orders/delivery_notes/:id',               component: SalesDeliveryNote},
    {path: '/sales_orders/invoices',                         component: SalesInvoices},
    {path: '/sales_orders/invoices/:id',                     component: SalesInvoice},
    {path: '/sales_orders/orders',                           component: SalesOrders},
    //{path: '/sales_orders/orders/new',                       component: SalesFormSales},
    {path: '/sales_orders/orders/fulfill/:id',               component: SalesOrderFulfill},
    {path: '/sales_orders/orders/:id',                       component: SalesOrder},
    {path: '/sales_orders/reports',                          component: SalesReports},
    {path: '/sales_orders/returns',                          component: SalesReturns},
    {path: '/sales_orders/returns/:id',                      component: SalesReturn},
    {path: '/sales_orders/quotations',                       component: SalesQuotations},
    {path: '/sales_orders/quotations/new',                   component: SalesFormQuotation},
    {path: '/sales_orders/quotations/:id',                   component: SalesQuotation},
    
    //Staff of the Month
    {path: '/staff_month',                                  component: SOMWinners},
    {path: '/staff_month/admin',                            component: SOMAdmin},
    {path: '/staff_month/winners',                          component: SOMWinners},

    //Ticketing
    {path: '/ticketing',                                    component: TicketPersonal},
    {path: '/ticketing/admin',                              component: TicketAdmin},
    {path: '/ticketing/department',                         component: TicketDepartment},
    {path: '/ticketing/settings',                           component: TicketSetting},
    {path: '/ticketing/:id',                                component: TicketSingle},
    
    {path: '/users',                                        component: UmsUsers},
    {path: '/:pathMatch(.*)*',                              component: Error404},
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

export function registerGlobalComponents(app) {
    
    app.component('ApprovalBatches',                        ApprovalBatches);
    app.component('ApprovalDashboard',                      ApprovalDashboard);
    //app.component('ApprovalDocument',                       ApprovalDocument);
    //app.component('ApprovalDocuments',                      ApprovalDocuments);
    //app.component('ApprovalPurchaseOrder',                  ApprovalPurchaseOrder);
    app.component('ApprovalOrderReturn',                    ApprovalOrderReturn);
    app.component('ApprovalOrderReturns',                   ApprovalOrderReturns);
    app.component('ApprovalExpenses',                       ApprovalExpenses);
    app.component('ApprovalInvoices',                       ApprovalInvoices);
    app.component('ApprovalPurchaseRequests',               ApprovalPurchaseRequests);
    app.component('ApprovalSalesOrder',                     ApprovalSalesOrder);
    app.component('ApprovalSalesOrders',                    ApprovalSalesOrders);

        //app.component('ApprovalDetailBackupList',           ApprovalDetailBackupList);
        //app.component('ApprovalDetailCategoryList',         ApprovalDetailCategoryList);
        //app.component('ApprovalDetailDocumentList',         ApprovalDetailDocumentList);
        //app.component('ApprovalFormDocument',               ApprovalFormDocument);
        //app.component('ApprovalFormDocumentSearch',         ApprovalFormDocumentSearch);
        app.component('ApprovalFormAction',                 ApprovalFormAction);
        app.component('ApprovalFormInvoice',                ApprovalFormInvoice);
        app.component('ApprovalFormSalesOrder',             ApprovalFormSalesOrder);
        app.component('ApprovalFormSalesQuotation',         ApprovalFormSalesQuotation);
        
    app.component('ArchiveBackups',                         ArchiveBackups);
    app.component('ArchiveCategories',                      ArchiveCategories);
    app.component('ArchiveCategory',                        ArchiveCategory);
    app.component('ArchiveDashboard',                       ArchiveDashboard);
    app.component('ArchiveDocument',                        ArchiveDocument);
    app.component('ArchiveDocuments',                       ArchiveDocuments);

        app.component('ArchiveDetailBackupList',            ArchiveDetailBackupList);
        app.component('ArchiveDetailCategoryList',          ArchiveDetailCategoryList);
        app.component('ArchiveDetailDocumentList',          ArchiveDetailDocumentList);

        app.component('ArchiveFormBackup',                  ArchiveFormBackup);
        app.component('ArchiveFormCategory',                ArchiveFormCategory);
        app.component('ArchiveFormDocument',                ArchiveFormDocument);
        app.component('ArchiveFormDocumentSearch',          ArchiveFormDocumentSearch);

    app.component('ChatCompose',                            ChatCompose);
    app.component('ChatDashboard',                          ChatDashboard);
    app.component('ChatInbox',                              ChatInbox);
    app.component('ChatMessage',                            ChatMessage);
    app.component('ChatMessages',                           ChatMessages);
    app.component('ChatOutbox',                             ChatOutbox);
    app.component('ChatRoom',                               ChatRoom);
    app.component('ChatRooms',                              ChatRooms);

        app.component('ChatDetailMessageList',              ChatDetailMessageList);
        app.component('ChatDetailMessageView',              ChatDetailMessageView);
        app.component('ChatDetailRoomList',                 ChatDetailRoomList);

        app.component('ChatFormMessage',                    ChatFormMessage);
    
    app.component('CRMCustomer',                            CRMCustomer);
    app.component('CRMCustomers',                           CRMCustomers);
    app.component('CRMDashboard',                           CRMDashboard);
    app.component('CRMLead',                                CRMLead);
    app.component('CRMLeads',                               CRMLeads);

        app.component('CRMDetailContactList',                   CRMDetailContactList);
        app.component('CRMDetailCustomerList',                  CRMDetailCustomerList);
        app.component('CRMDetailCustomerSummary',               CRMDetailCustomerSummary);
        app.component('CRMDetailLead',                          CRMDetailLead);
        app.component('CRMDetailLeadList',                      CRMDetailLeadList);
        
        app.component('CRMFormContact',                         CRMFormContact);
        app.component('CRMFormCustomer',                        CRMFormCustomer);
        app.component('CRMFormCustomerUpload',                  CRMFormCustomerUpload);
        app.component('CRMFormLead',                            CRMFormLead);        

    app.component('DashboardMain',                          DashboardMain);

    app.component('EMRConsultantConsultation',              EMRConsultantConsultation);
    app.component('EMRConsultantMyPastConsultations',       EMRConsultantMyPastConsultations);
    app.component('EMRConsultantQueue',                     EMRConsultantQueue);

        app.component('EMRConsultantDetailConsultation',    EMRConsultantDetailConsultation);
        app.component('EMRConsultantDetailQueue',           EMRConsultantDetailQueue);
        app.component('EMRConsultantDetailQueueList',       EMRConsultantDetailQueueList);
        app.component('EMRConsultantDetailResultQueue',     EMRConsultantDetailResultQueue);
        app.component('EMRConsultantDetailReview',          EMRConsultantDetailReview);
        app.component('EMRConsultantDetailSummary',         EMRConsultantDetailSummary);

        app.component('EMRConsultantFormConsultation',      EMRConsultantFormConsultation);
        app.component('EMRConsultantFormHistory',           EMRConsultantFormHistory);
        app.component('EMRConsultantFormLaboratory',        EMRConsultantFormLaboratory);
        app.component('EMRConsultantFormPrescription',      EMRConsultantFormPrescription);
        app.component('EMRConsultantFormRadiology',         EMRConsultantFormRadiology);
        app.component('EMRConsultantFormSoapNote',          EMRConsultantFormSoapNote);

    
    app.component('EMRFrontOfficeDashboard',                EMRFrontOfficeDashboard),
    app.component('EMRFrontOfficeAppointment',              EMRFrontOfficeAppointment),
    app.component('EMRFrontOfficeAppointments',             EMRFrontOfficeAppointments),

        app.component('EMRFrontOfficeFormAppointment',          EMRFrontOfficeFormAppointment);
        app.component('EMRFrontOfficeFormCheckIn',              EMRFrontOfficeFormCheckIn);
    
    app.component('EMRLaboratoryDashboard',                 EMRLaboratoryDashboard);

    //app.component('EMRDashboard',                                       EMRDashboard);
    //app.component('EMRQueueDoctor',                                     EMRQueueDoctor);
    

    app.component('EMRNursingDashboard',                       EMRNursingDashboard);

    app.component('EMROperationsBranch',                        EMROperationsBranch);
    app.component('EMROperationsBranches',                      EMROperationsBranches);
    app.component('EMROperationsDashboard',                     EMROperationsDashboard);
    app.component('EMROperationsDepartments',                   EMROperationsDepartments);
    app.component('EMROperationsPricelist',                     EMROperationsPricelist);
    app.component('EMROperationsPricelists',                    EMROperationsPricelists);
    app.component('EMROperationsService',                       EMROperationsService);
    app.component('EMROperationsServices',                      EMROperationsServices);
    app.component('EMROperationsServiceTypes',                  EMROperationsServiceTypes);



    app.component('EMRPatientAll',                             EMRPatientAll);

    app.component('EMRPatientAllergies',                       EMRPatientAllergies);
    app.component('EMRPatientContacts',                        EMRPatientContacts);
    app.component('EMRPatientPrescriptions',                   EMRPatientPrescriptions);
    app.component('EMRPatientSearch' ,                         EMRPatientSearch);
    app.component('EMRPatientSingle',                          EMRPatientSingle);
    app.component('EMRPatientVitals',                          EMRPatientVitals);

        //app.component('EMRFormAllergy',                        EMRFormAllergy);
        //app.component('EMRFormContact',                        EMRFormContact);
        //app.component('EMRFormPassword',                       EMRFormPassword);
        //app.component('EMRFormPatientService',                 EMRFormPatientService);

        app.component('EMRPatientDetailAllergies',             EMRPatientDetailAllergies);
        app.component('EMRPatientDetailBioData',               EMRPatientDetailBioData);
        app.component('EMRPatientDetailCard',                  EMRPatientDetailCard);
        app.component('EMRPatientDetailFull',                  EMRPatientDetailFull);
        app.component('EMRPatientDetailNextOfKin',             EMRPatientDetailNextOfKin);
        app.component('EMRPatientDetailPatientList',           EMRPatientDetailPatientList); 
        app.component('EMRPatientDetailPendingTransactions',   EMRPatientDetailPendingTransactions);
        app.component('EMRPatientDetailInsurances',            EMRPatientDetailInsurances);
        
        app.component('EMRPatientFormAllergy',                 EMRPatientFormAllergy);
        app.component('EMRPatientFormContact',                 EMRPatientFormContact);
        app.component('EMRPatientFormInsurance',               EMRPatientFormInsurance);
        app.component('EMRPatientFormPassword',                EMRPatientFormPassword);
        app.component('EMRPatientFormPatient',                 EMRPatientFormPatient);   
        app.component('EMRPatientFormPrescription',            EMRPatientFormPrescription);
        app.component('EMRPatientFormRegistration',            EMRPatientFormRegistration);
        app.component('EMRPatientFormSearch',                  EMRPatientFormSearch);
        app.component('EMRPatientFormVital',                   EMRPatientFormVital);
        
    
    //app.component('EMRLaboratoryMyPastConsultations',      EMRLaboratoryMyPastConsultations);
    //app.component('EMRLaboratoryQueue',                    EMRLaboratoryQueue);
    
    app.component('EMRRadiologyDashboard',                  EMRRadiologyDashboard);
    app.component('EMRRadiologyInsurance',                  EMRRadiologyInsurance);
    app.component('EMRRadiologyQueue',                      EMRRadiologyQueue);
    app.component('EMRRadiologyReferredIn',                 EMRRadiologyReferredIn);
    app.component('EMRRadiologyReferredOut',                EMRRadiologyReferredOut);
    
        app.component('EMRRadiologyDetailReferralList',         EMRRadiologyDetailReferralList);
        app.component('EMRRadiologyDetailRequestList',          EMRRadiologyDetailRequestList);
    
    app.component('EMRVisitAll',                            EMRVisitAll);
    app.component('EMRVisitBill',                           EMRVisitBill);
    app.component('EMRVisitDashboard',                      EMRVisitDashboard);
    app.component('EMRVisitSingle',                         EMRVisitSingle);

        app.component('EMRVisitDetailList',                 EMRVisitDetailList);
        app.component('EMRVisitDetailSummary',              EMRVisitDetailSummary);

    app.component('EquipmentAsset',                              EquipmentAsset);
    app.component('EquipmentAssets',                             EquipmentAssets);
    app.component('EquipmentDashboard',                          EquipmentDashboard);
    app.component('EquipmentMaintenance',                        EquipmentMaintenance);
    app.component('EquipmentMaintenances',                       EquipmentMaintenances);
    app.component('EquipmentSchedule',                           EquipmentSchedule);
    app.component('EquipmentSchedules',                          EquipmentSchedules);

        app.component('EquipmentDetailAsset',                       EquipmentDetailAsset);
        app.component('EquipmentDetailAssetList',                   EquipmentDetailAssetList);
        app.component('EquipmentDetailMaintenance',                 EquipmentDetailMaintenance);
        app.component('EquipmentDetailMaintenanceList',             EquipmentDetailMaintenanceList);
        app.component('EquipmentDetailSchedule',                    EquipmentDetailSchedule);
        app.component('EquipmentDetailScheduleList',                EquipmentDetailScheduleList);
        app.component('EquipmentDetailTransfer',                    EquipmentDetailTransfer);
        app.component('EquipmentDetailTransferList',                EquipmentDetailTransferList);

        app.component('EquipmentFormAsset',                         EquipmentFormAsset);
        app.component('EquipmentFormMaintenance',                   EquipmentFormMaintenance);
        app.component('EquipmentFormSchedule',                      EquipmentFormSchedule);
        app.component('EquipmentFormTransfer',                      EquipmentFormTransfer);

   
    app.component('EscrowAdminDashboard',                        EscrowAdminDashboard);
    app.component('EscrowAdminDispute',                          EscrowAdminDispute);
    app.component('EscrowAdminDisputes',                         EscrowAdminDisputes);
    app.component('EscrowAdminProduct',                          EscrowAdminProduct);
    app.component('EscrowAdminProducts',                         EscrowAdminProducts);
    app.component('EscrowAdminTransaction',                      EscrowAdminTransaction);
    app.component('EscrowAdminTransactions',                     EscrowAdminTransactions);

    app.component('EscrowDashboard',                             EscrowDashboard);
    app.component('EscrowDispute',                               EscrowDispute);
    app.component('EscrowDisputes',                              EscrowDisputes);
    app.component('EscrowProduct',                               EscrowProduct);
    app.component('EscrowProducts',                              EscrowProducts);
    app.component('EscrowTransaction',                           EscrowTransaction);
    app.component('EscrowTransactions',                          EscrowTransactions);

        app.component('EscrowDetailDispute',                            EscrowDetailDispute);
        app.component('EscrowDetailDisputeList',                        EscrowDetailDisputeList);
        app.component('EscrowDetailPayment',                            EscrowDetailPayment);
        app.component('EscrowDetailPaymentList',                        EscrowDetailPaymentList);
        app.component('EscrowDetailProduct',                            EscrowDetailProduct);
        app.component('EscrowDetailProductList',                        EscrowDetailProductList);
        app.component('EscrowDetailTransaction',                        EscrowDetailTransaction);
        app.component('EscrowDetailTransactionList',                    EscrowDetailTransactionList);

        app.component('EscrowFormAccept',                               EscrowFormAccept);
        app.component('EscrowFormDispute',                              EscrowFormDispute);
        app.component('EscrowFormProduct',                              EscrowFormProduct);
        app.component('EscrowFormTransaction',                          EscrowFormTransactionProduct);
        app.component('EscrowFormTransactionProduct',                   EscrowFormTransactionProduct);
        app.component('EscrowFormTransactionRequest',                   EscrowFormTransactionRequest);

    app.component('FacilityDashboard',                      FacilityDashboard);
    app.component('FacilitySpaces',                         FacilitySpaces);

    app.component('FinanceDashboard',                       FinanceDashboard);
    app.component('FinanceAsset',                           FinanceAsset);
    app.component('FinanceAssets',                          FinanceAssets);
    app.component('FinanceExpense',                         FinanceExpense);
    app.component('FinanceExpenses',                        FinanceExpenses);
    app.component('FinanceExpenseTypes',                    FinanceExpenseTypes);
    app.component('FinanceInvoice',                         FinanceInvoice);
    app.component('FinanceInvoices',                        FinanceInvoices);
    app.component('FinancePayment',                         FinancePayment);
    app.component('FinancePayments',                        FinancePayments);
    app.component('FinancePaymentModes',                    FinancePaymentModes);
    app.component('FinancePayOut',                          FinancePayOut);
    app.component('FinancePayOuts',                         FinancePayOuts);
    app.component('FinancePricelist',                       FinancePricelist);
    app.component('FinancePricelists',                      FinancePricelists);
    app.component('FinanceReports',                         FinanceReports);
    app.component('FinanceTransaction',                     FinanceTransaction);
    app.component('FinanceTransactions',                    FinanceTransactions);

    
        app.component('FinanceDetailAssetList',                         FinanceDetailAssetList);
        app.component('FinanceDetailBranchAccountList',                 FinanceDetailBranchAccountList);
        app.component('FinanceDetailBranchPricelistList',               FinanceDetailBranchPricelistList);
        app.component('FinanceDetailExpense',                           FinanceDetailExpense);
        app.component('FinanceDetailExpenseList',                       FinanceDetailExpenseList);
        app.component('FinanceDetailIncome',                            FinanceDetailIncome);
        app.component('FinanceDetailIncomeList',                        FinanceDetailIncomeList);
        app.component('FinanceDetailInvoice',                           FinanceDetailInvoice);
        app.component('FinanceDetailInvoiceList',                       FinanceDetailInvoiceList);
        app.component('FinanceDetailPayment',                           FinanceDetailPayment);
        app.component('FinanceDetailPaymentAllocationList',             FinanceDetailPaymentAllocationList);
        app.component('FinanceDetailPaymentList',                       FinanceDetailPaymentList);
        app.component('FinanceDetailPayOut',                            FinanceDetailPayOut);
        app.component('FinanceDetailPayOutAllocationList',              FinanceDetailPayOutAllocationList);
        app.component('FinanceDetailPayOutList',                        FinanceDetailPayOutList);
        app.component('FinanceDetailPaymentModeList',                   FinanceDetailPaymentModeList);
        app.component('FinanceDetailPricelist',                         FinanceDetailPricelist);
        app.component('FinanceDetailPricelistList',                     FinanceDetailPricelistList);
        app.component('FinanceDetailPricelistPlanList',                 FinanceDetailPricelistPlanList);
        app.component('FinanceDetailTransaction',                       FinanceDetailTransaction);
        app.component('FinanceDetailTransactionList',                   FinanceDetailTransactionList);
        app.component('FinanceDetailReportAgingAnalysisReceivables',    FinanceDetailReportAgingAnalysisReceivables);
        app.component('FinanceDetailReportBalanceSheet',                FinanceDetailReportBalanceSheet);

        app.component('FinanceFormBranchAccount',               FinanceFormBranchAccount);
        app.component('FinanceFormBranchPricelist',             FinanceFormBranchPricelist);
        app.component('FinanceFormExpense',                     FinanceFormExpense);
        app.component('FinanceFormExpenseType',                 FinanceFormExpenseType);
        app.component('FinanceFormIncome',                      FinanceFormIncome);
        app.component('FinanceFormInvoice',                     FinanceFormInvoice);
        app.component('FinanceFormPayment',                     FinanceFormPayment);
        app.component('FinanceFormPaymentMode',                 FinanceFormPaymentMode);
        app.component('FinanceFormPayOut',                      FinanceFormPayOut);
        app.component('FinanceFormPricelist',                   FinanceFormPricelist);   
        app.component('FinanceFormPricelistItemBulk',           FinanceFormPricelistItemBulk);
        app.component('FinanceFormTransaction',                 FinanceFormTransaction);
        

    app.component('GeneralChartBar',                            GeneralChartBar);
    app.component('GeneralChartDonut',                          GeneralChartDonut);
    app.component('GeneralFormTab',                             GeneralFormTab);
    app.component('GeneralFormWizard',                          GeneralFormWizard);

    app.component('HeaderBranch', HeaderBranch);

    app.component('HrmsDashboard',              HrmsDashboard);

    app.component('HrmsAssessments',            HrmsAssessments);
    app.component('HrmsAssessmentsAdmin',       HrmsAssessmentsAdmin);
    app.component('HrmsAssessmentsTeam',        HrmsAssessmentsTeam);
    app.component('HrmsAssessmentHrItems',      HrmsAssessmentHrItems);
    app.component('HrmsAttendanceSummaries',    HrmsAttendanceSummaries);
    app.component('HrmsAttendanceSummary',      HrmsAttendanceSummary);
    app.component('HrmsClockIns',               HrmsClockIns);
    app.component('HrmsDesignations',           HrmsDesignations);
    app.component('HrmsDesignation',            HrmsDesignation);
    app.component('HrmsEducations',             HrmsEducations);
    app.component('HrmsEmployee',               HrmsEmployee);
    app.component('HrmsEmployeeBonus',          HrmsEmployeeBonus);
    app.component('HrmsEmployeeBonuses',        HrmsEmployeeBonuses);
    app.component('HrmsEmployeeContact',        HrmsEmployeeContact);
    app.component('HrmsEmployeeLeaveType',      HrmsEmployeeLeaveType);
    app.component('HrmsEmployeeLeaveTypes',     HrmsEmployeeLeaveTypes);
    app.component('HrmsEmployees',              HrmsEmployees);
    app.component('HrmsJob',                    HrmsJob);
    app.component('HrmsJobs',                   HrmsJobs);
    app.component('HrmsLeaveAllowanceMine',     HrmsLeaveAllowanceMine);
    app.component('HrmsLeaveAllowances',        HrmsLeaveAllowances);
    app.component('HrmsLeaveRequest',           HrmsLeaveRequest);
    app.component('HrmsLeaveRequests',          HrmsLeaveRequests);
    app.component('HrmsLeaveRequestsAdmin',     HrmsLeaveRequestsAdmin);
    app.component('HrmsLeaveRequestsTeam',      HrmsLeaveRequestsTeam);
    app.component('HrmsLeaveType',              HrmsLeaveType);
    app.component('HrmsLeaveTypes',             HrmsLeaveTypes);
    app.component('HrmsLeaveUserLeaveTypes',    HrmsLeaveUserLeaveTypes);
    app.component('HrmsPayslips',               HrmsPayslips);
    app.component('HrmsTrainings',              HrmsTrainings);
    
        app.component('HrmsDetailAccountList',                  HrmsDetailAccountList);
        app.component('HrmsDetailAssessmentHrItemList',         HrmsDetailAssessmentHrItemList);
        app.component('HrmsDetailAssessmentList',               HrmsDetailAssessmentList);
        app.component('HrmsDetailAssessmentPeriod',             HrmsDetailAssessmentPeriod);
        app.component('HrmsDetailAttendanceSummary',            HrmsDetailAttendanceSummary);
        app.component('HrmsDetailAttendanceSummaryList',        HrmsDetailAttendanceSummaryList);
        app.component('HrmsDetailClockInList',                  HrmsDetailClockInList);
        app.component('HrmsDetailEducation',                    HrmsDetailEducation);
        app.component('HrmsDetailEducationList',                HrmsDetailEducationList);
        app.component('HrmsDetailDesignation',                  HrmsDetailDesignation);
        app.component('HrmsDetailDesignationKpiList',           HrmsDetailDesignationKpiList);
        app.component('HrmsDetailEmployee',                     HrmsDetailEmployee);
        app.component('HrmsDetailEmployeeBonus',                HrmsDetailEmployeeBonus);
        app.component('HrmsDetailEmployeeBonusList',            HrmsDetailEmployeeBonusList);
        app.component('HrmsDetailEmployeeCard',                 HrmsDetailEmployeeCard);
        app.component('HrmsDetailEmployeeDeduction',            HrmsDetailEmployeeDeduction);
        app.component('HrmsDetailEmployeeDeductionList',        HrmsDetailEmployeeDeductionList);
        app.component('HrmsDetailEmployeeLeaveType',            HrmsDetailEmployeeLeaveType);
        app.component('HrmsDetailEmployeeLeaveTypeList',        HrmsDetailEmployeeLeaveTypeList);
        app.component('HrmsDetailEmployeeLeaveTypes',           HrmsDetailEmployeeLeaveTypes);
        app.component('HrmsDetailEmployeeList',                 HrmsDetailEmployeeList);
        app.component('HrmsDetailJobList',                      HrmsDetailJobList);
        app.component('HrmsDetailLeaveAllowanceList',           HrmsDetailLeaveAllowanceList);
        app.component('HrmsDetailLeaveRequest',                 HrmsDetailLeaveRequest);
        app.component('HrmsDetailLeaveRequestList',             HrmsDetailLeaveRequestList);
        app.component('HrmsDetailPayslipList',                  HrmsDetailPayslipList);
        app.component('HrmsDetailTraining',                     HrmsDetailTraining);
        app.component('HrmsDetailTrainingList',                 HrmsDetailTrainingList);

        app.component('HrmsFormAssessmentHrItem',               HrmsFormAssessmentHrItem); 
        app.component('HrmsFormAssessmentPeriod',               HrmsFormAssessmentPeriod);
        app.component('HrmsFormAttendanceSummary',              HrmsFormAttendanceSummary);
        app.component('HrmsFormDesignation',                    HrmsFormDesignation);
        app.component('HrmsFormEducation',                      HrmsFormEducation);
        app.component('HrmsFormEmployee',                       HrmsFormEmployee);
        app.component('HrmsFormEmployeeAssignManager',          HrmsFormEmployeeAssignManager);
        app.component('HrmsFormEmployeeBonus',                  HrmsFormEmployeeBonus);
        app.component('HrmsFormEmployeeDeduction',              HrmsFormEmployeeDeduction);
        app.component('HrmsFormEmployeeImport',                 HrmsFormEmployeeImport);
        app.component('HrmsFormEmployeeLeaveType',              HrmsFormEmployeeLeaveType);
        app.component('HrmsFormEmployeeStatus',                 HrmsFormEmployeeStatus);
        app.component('HrmsFormLeaveAllowance',                 HrmsFormLeaveAllowance);
        app.component('HrmsFormLeaveAllowanceConfirm',          HrmsFormLeaveAllowanceConfirm);
        app.component('HrmsFormLeaveRequest',                   HrmsFormLeaveRequest);
        app.component('HrmsFormLeaveRequestConfirm',            HrmsFormLeaveRequestConfirm);
        app.component('HrmsFormLeaveRequestImport',             HrmsFormLeaveRequestImport);
        app.component('HrmsFormLeaveType',                      HrmsFormLeaveType);

/*
    app.component('InsuranceClaims',                    InsuranceClaims);
    app.component('InsuranceDashboard',                 InsuranceDashboard);
    app.component('InsurancePlan',                      InsurancePlan);
    app.component('InsurancePlans',                     InsurancePlans);
    app.component('InsuranceProvider',                  InsuranceProvider);
    app.component('InsuranceProvidersSuspended',        InsuranceProvidersSuspended);
    app.component('InsuranceProviders',                 InsuranceProviders);
    app.component('InsuranceQueueAuthorizations',       InsuranceQueueAuthorizations);
    app.component('InsuranceQueueCoPaid',               InsuranceQueueCoPaid);
    app.component('InsuranceQueueUncovered',            InsuranceQueueUncovered);
    
        app.component('InsuranceDetailProviderContacts',       InsuranceDetailProviderContacts);
        app.component('InsuranceDetailProviderPlans',          InsuranceDetailProviderPlans);
        app.component('InsuranceDetailSummaryPlan',            InsuranceDetailSummaryPlan);
        app.component('InsuranceDetailTransaction',            InsuranceDetailTransaction);
        app.component('InsuranceDetailTransactionList',        InsuranceDetailTransactionList);
    
        app.component('InsuranceFormAuthCode',                 InsuranceFormAuthCode);
        app.component('InsuranceFormAuthRequest',              InsuranceFormAuthRequest);
        app.component('InsuranceFormContact',                  InsuranceFormContact);
        app.component('InsuranceFormPlan',                     InsuranceFormPlan);
        app.component('InsuranceFormPlanBranch',               InsuranceFormPlanBranch);
        app.component('InsuranceFormProvider',                 InsuranceFormProvider);
*/        
    app.component('InventoryBrands',                            InventoryBrands);
    app.component('InventoryCategories',                        InventoryCategories);
    app.component('InventoryCategory',                          InventoryCategory);
    app.component('InventoryClassifications',                   InventoryClassifications);
    app.component('InventoryDashboard',                         InventoryDashboard);
    app.component('InventoryDirectPurchase',                    InventoryDirectPurchase);
    app.component('InventoryDirectPurchases',                   InventoryDirectPurchases);
    app.component('InventoryItem',                              InventoryItem);
    app.component('InventoryItems',                             InventoryItems);
    app.component('InventoryItemsBulk',                         InventoryItemsBulk);
    app.component('InventoryPackage',                           InventoryPackage);
    app.component('InventoryPackages',                          InventoryPackages);
    app.component('InventorySalesOrder',                        InventorySalesOrder);
    app.component('InventorySalesOrders',                       InventorySalesOrders);
    app.component('InventoryStore',                             InventoryStore);
    app.component('InventoryStores',                            InventoryStores);
    app.component('InventoryTransferOrder',                     InventoryTransferOrder);
    app.component('InventoryTransferOrdersIn',                  InventoryTransferOrdersIn);
    app.component('InventoryTransferOrdersOut',                 InventoryTransferOrdersOut);

        app.component('InventoryDetailBrand',                   InventoryDetailBrand);
        app.component('InventoryDetailItem',                    InventoryDetailItem);
        //app.component('InventoryDetailItemBulk',                InventoryDetailItemBulk);
        app.component('InventoryDetailItemList',                InventoryDetailItemList);
        app.component('InventoryDetailStoreExpired',            InventoryDetailStoreExpired);
        app.component('InventoryDetailStoreItem',               InventoryDetailStoreItem);
        app.component('InventoryDetailStoreItemList',           InventoryDetailStoreItemList);
        app.component('InventoryDetailStoreSoonToExpire',       InventoryDetailStoreSoonToExpire);
        app.component('InventoryDetailStoreSummary',            InventoryDetailStoreSummary);
        app.component('InventoryDetailTransferOrder',           InventoryDetailTransferOrder);
        app.component('InventoryDetailTransferOrderList',       InventoryDetailTransferOrderList);

        app.component('InventoryFormBrand',                     InventoryFormBrand);
        app.component('InventoryFormCategory',                  InventoryFormCategory);
        app.component('InventoryFormClassification',            InventoryFormClassification);
        app.component('InventoryFormFulfill',                   InventoryFormFulfill);
        app.component('InventoryFormFulfillment',               InventoryFormFulfillment);
        app.component('InventoryFormItem',                      InventoryFormItem);
        app.component('InventoryFormItemBulk',                  InventoryFormItemBulk);
        app.component('InventoryFormItemImport',                InventoryFormItemImport);
        app.component('InventoryFormItemSearch',                InventoryFormItemSearch);
        app.component('InventoryFormItemType',                  InventoryFormItemType);
        app.component('InventoryFormSalesOrder',                InventoryFormSalesOrder);
        app.component('InventoryFormStore',                     InventoryFormStore);
        app.component('InventoryFormStoreIssue',                InventoryFormStoreIssue);
        app.component('InventoryFormStoreItemSetting',          InventoryFormStoreItemSetting);
        app.component('InventoryFormTransferOrder',             InventoryFormTransferOrder);
        app.component('InventoryFormTransferOrderReject',       InventoryFormTransferOrderReject);

    app.component('LearnCategories',    LearnCategories);
    app.component('LearnSubCategory',   LearnSubCategory);
    
    app.component('LearnAdminCourse',        LearnAdminCourse);
    app.component('LearnAdminCourses',       LearnAdminCourses);
    app.component('LearnAdminDashboard',     LearnAdminDashboard);
    app.component('LearnAdminExam',          LearnAdminExam);
    app.component('LearnAdminExams',         LearnAdminExams);
    app.component('LearnAdminExamResult',    LearnAdminExamResult);
    app.component('LearnAdminExamResults',   LearnAdminExamResults);
    app.component('LearnAdminUserCourse',    LearnAdminUserCourse);
    app.component('LearnAdminUserCourses',   LearnAdminUserCourses);
    app.component('LearnCourse',             LearnCourse);
    app.component('LearnCourses',            LearnCourses);
    app.component('LearnExams',              LearnExams);
    app.component('LearnLesson',             LearnLesson);
    app.component('LearnLessons',            LearnLessons);
    app.component('LearnTutorCourse',        LearnTutorCourse);
    app.component('LearnTutorCourses',       LearnTutorCourses);
    app.component('LearnTutorExams',         LearnTutorExams);
    app.component('LearnTutorLessons',       LearnTutorLessons);
    app.component('LearnTutorExamResults',   LearnTutorExamResults);

        app.component('LearnDetailAssignTutor',     LearnDetailAssignTutor);
        app.component('LearnDetailAssignUser',      LearnDetailAssignUser);
        app.component('LearnDetailCategory',        LearnDetailCategory);
        app.component('LearnDetailCourse',          LearnDetailCourse);
        app.component('LearnDetailCourseList',      LearnDetailCourseList);
        app.component('LearnDetailExam',            LearnDetailExam);
        app.component('LearnDetailLesson',          LearnDetailLesson);
        app.component('LearnDetailLessonList',      LearnDetailLessonList);
        app.component('LearnDetailOptionList',      LearnDetailOptionList);
        app.component('LearnDetailQuestion',        LearnDetailQuestion);
        app.component('LearnDetailSubCategory',     LearnDetailSubCategory);
        app.component('LearnDetailUserCourse',      LearnDetailUserCourse);
        app.component('LearnDetailUserCourseList',  LearnDetailUserCourseList);
        app.component('LearnDetailUserExam',        LearnDetailUserExam);
        app.component('LearnDetailUserExamList',    LearnDetailUserExamList);

        app.component('LearnFormAssignTutor',     LearnFormAssignTutor);
        app.component('LearnFormAssignUser',      LearnFormAssignUser);
        app.component('LearnFormCategory',        LearnFormCategory);
        app.component('LearnFormCourse',          LearnFormCourse);
        app.component('LearnFormExam',            LearnFormExam);
        app.component('LearnFormLesson',          LearnFormLesson);
        app.component('LearnFormLessons',         LearnFormLessons);
        app.component('LearnFormOption',          LearnFormOption);
        app.component('LearnFormQuestion',        LearnFormQuestion);
        app.component('LearnFormSubCategory',     LearnFormSubCategory);



    app.component('LoanDashboard',      LoanDashboard);

    app.component('NoticeAll',          NoticeAll);
    app.component('NoticeAdmin',        NoticeAdmin);
    app.component('NoticeBoard',        NoticeBoard);
    app.component('NoticeSingle',       NoticeSingle);

        app.component('NoticeDetailList',           NoticeDetailList);
        
        app.component('NoticeForm',                 NoticeForm);     
       
        
    app.component('OperationBranch',                OperationBranch);     
    app.component('OperationBranches',              OperationBranches);    
    app.component('OperationDashboard',             OperationDashboard);  
    app.component('OperationDepartment',            OperationDepartment);
    app.component('OperationDepartments',           OperationDepartments);
    app.component('OperationPriceList',             OperationPriceList); 
    app.component('OperationPriceLists',            OperationPriceLists);

        app.component('OperationDetailBranch',                  OperationDetailBranch);
        app.component('OperationDetailBranchList',              OperationDetailBranchList);
        app.component('OperationDetailDepartmentList',          OperationDetailDepartmentList);
        app.component('OperationDetailPricelistList',           OperationDetailPricelistList);
        app.component('OperationDetailServiceList',             OperationDetailServiceList);

        app.component('OperationFormBranch',                    OperationFormBranch);
        app.component('OperationFormDepartment',                OperationFormDepartment);
        app.component('OperationFormService',                   OperationFormService);
        app.component('OperationFormServiceType',               OperationFormServiceType);
    
    app.component('PoliciesAdmin',                  PoliciesAdmin);
    app.component('PoliciesDepartmental',           PoliciesDepartmental);
    app.component('PoliciesGeneral',                PoliciesGeneral);
    app.component('PoliciesSingle',                 PoliciesSingle);

        app.component('PoliciesDetailList',             PoliciesDetailList);
        app.component('PoliciesFormAssign',             PoliciesFormAssign);
        app.component('PoliciesFormNew',                PoliciesFormNew);
        
    app.component('ProcurementApprovalMatrices',    ProcurementApprovalMatrices);
    app.component('ProcurementDashboard',           ProcurementDashboard);
    app.component('ProcurementPurchaseOrder',       ProcurementPurchaseOrder);
    app.component('ProcurementPurchaseOrderNew',    ProcurementPurchaseOrderNew);
    app.component('ProcurementPurchaseOrders',      ProcurementPurchaseOrders);
    app.component('ProcurementVendor',              ProcurementVendor);
    app.component('ProcurementVendorCategories',    ProcurementVendorCategories);
    app.component('ProcurementVendors',             ProcurementVendors);
    app.component('ProcurementWorkOrder',           ProcurementWorkOrder);
    app.component('ProcurementWorkOrders',          ProcurementWorkOrders);
    app.component('ProcurementSettings',            ProcurementSettings);    
    
        app.component('ProcurementDetailApprovalMatrix',                        ProcurementDetailApprovalMatrix);
        app.component('ProcurementDetailApprovalTrail',                         ProcurementDetailApprovalTrail);
        app.component('ProcurementDetailBatch',                                 ProcurementDetailBatch);
        app.component('ProcurementDetailBatchList',                             ProcurementDetailBatchList);
        app.component('ProcurementDetailPurchaseOrder',                         ProcurementDetailPurchaseOrder);
        app.component('ProcurementDetailPurchaseOrderApprovalList',             ProcurementDetailPurchaseOrderApprovalList);
        app.component('ProcurementDetailPurchaseOrderItemList',                 ProcurementDetailPurchaseOrderItemList);
        app.component('ProcurementDetailPurchaseOrderList',                     ProcurementDetailPurchaseOrderList);
        app.component('ProcurementDetailPurchaseOrderSummary',                  ProcurementDetailPurchaseOrderSummary);
        app.component('ProcurementDetailStatusRibbon',                          ProcurementDetailStatusRibbon);
        app.component('ProcurementDetailVendorContactList',                     ProcurementDetailVendorContactList);
        app.component('ProcurementDetailWorkOrder',                             ProcurementDetailWorkOrder);
        app.component('ProcurementDetailWorkOrderList',                         ProcurementDetailWorkOrderList);

        app.component('ProcurementFormAdditionalCost',                          ProcurementFormAdditionalCost);
        app.component('ProcurementFormApprovalMatrix',                          ProcurementFormApprovalMatrix);
        app.component('ProcurementFormAssignStore',                             ProcurementFormAssignStore);
        app.component('ProcurementFormAssignVendor',                            ProcurementFormAssignVendor);
        app.component('ProcurementFormBatch',                                   ProcurementFormBatch);
        app.component('ProcurementFormBatchApproval',                           ProcurementFormBatchApproval);
        app.component('ProcurementFormOtherCost',                               ProcurementFormOtherCost);
        app.component('ProcurementFormPaymentTerm',                             ProcurementFormPaymentTerm);
        app.component('ProcurementFormPurchaseOrder',                           ProcurementFormPurchaseOrder);
        app.component('ProcurementFormPurchaseOrderApproval',                   ProcurementFormPurchaseOrderApproval);
        app.component('ProcurementFormPurchaseOrderItem',                       ProcurementFormPurchaseOrderItem);
        //app.component('ProcurementFormPurchaseOrderItemList',                   ProcurementFormPurchaseOrderItemList);
        app.component('ProcurementFormVendor',                                  ProcurementFormVendor);
        app.component('ProcurementFormVendorCategory',                          ProcurementFormVendorCategory);
        app.component('ProcurementFormVendorContact',                           ProcurementFormVendorContact);
        app.component('ProcurementFormWorkOrder',                               ProcurementFormWorkOrder);
        app.component('ProcurementFormWorkOrderItem',                           ProcurementFormWorkOrderItem);

    app.component('SalesCustomer',                SalesCustomer);
    app.component('SalesCustomers',               SalesCustomers);
    app.component('SalesDashboard',               SalesDashboard);
    app.component('SalesDeliveryNote',            SalesDeliveryNote);
    app.component('SalesDeliveryNotes',           SalesDeliveryNotes);
    app.component('SalesInvoice',                 SalesInvoice);
    app.component('SalesInvoices',                SalesInvoices);
    app.component('SalesOrder',                   SalesOrder);
    app.component('SalesOrderFulfill',            SalesOrderFulfill);
    app.component('SalesOrders',                  SalesOrders);
    app.component('SalesReports',                 SalesReports);
        
        app.component('SalesDetailDeliveryNote',                SalesDetailDeliveryNote);
        app.component('SalesDetailDeliveryNoteList',            SalesDetailDeliveryNoteList);
        app.component('SalesDetailOrder',                       SalesDetailOrder);
        app.component('SalesDetailOrderList',                   SalesDetailOrderList);
        app.component('SalesDetailOrderItemList',               SalesDetailOrderItemList);
        app.component('SalesDetailOrderSummary',                SalesDetailOrderSummary); 
        app.component('SalesDetailReceipt',                     SalesDetailReceipt);
        app.component('SalesDetailReturn',                      SalesDetailReturn);
        app.component('SalesDetailReturnList',                  SalesDetailReturnList);
        app.component('SalesDetailQuotation',                   SalesDetailQuotation);
        app.component('SalesDetailQuotationList',               SalesDetailQuotationList);

        app.component('SalesDetailReportDailySales',            SalesDetailReportDailySales);
        app.component('SalesDetailReportSalesItemDetailed',     SalesDetailReportSalesItemDetailed);

        app.component('SalesFormDeliveryNote',                  SalesFormDeliveryNote);
        app.component('SalesFormFulfill',                       SalesFormFulfill); 
        app.component('SalesFormFulfillOrderItem',              SalesFormFulfillOrderItem);   
        app.component('SalesFormOrder',                         SalesFormOrder);
        app.component('SalesFormQuotation',                     SalesFormQuotation);
        app.component('SalesFormReturn',                        SalesFormReturn);
        //app.component('SalesFormSales',                         SalesFormSales)

    app.component('SOMAdmin',               SOMAdmin);
    app.component('SOMCloseNominations',    SOMCloseNominations);
    app.component('SOMDetail',              SOMDetail);
    
        app.component('SOMDetailNominations',       SOMDetailNominations);
        app.component('SOMDetailVotes',             SOMDetailVotes);
        app.component('SOMDetailWinners',           SOMDetailWinners);
        app.component('SOMFormMonth', SOMFormMonth);
    
    app.component('TicketAdmin',            TicketAdmin);
    app.component('TicketDepartment',       TicketDepartment);
    app.component('TicketPersonal',         TicketPersonal);
    app.component('TicketSingle',           TicketSingle);

        app.component('TicketDetailDashboard',      TicketDetailDashboard);
        app.component('TicketDetailList',           TicketDetailList);

        app.component('TicketFormAccept',   TicketFormAccept);
        app.component('TicketFormAssign',   TicketFormAssign);
        app.component('TicketFormComplete', TicketFormComplete);
        app.component('TicketFormNew',      TicketFormNew);
        app.component('TicketFormReply',    TicketFormReply);
    
    app.component('UmsBirthday',                UmsBirthday);
    app.component('UmsBirthdays',               UmsBirthdays);
    app.component('UmsCompleteRegistration',    UmsCompleteRegistration);
    app.component('UmsContact',                 UmsContact);
    app.component('UmsContacts',                UmsContacts);
    app.component('UmsProfile',                 UmsProfile);
    app.component('UmsRole',                    UmsRole);
    app.component('UmsRoles',                   UmsRoles);
    app.component('UmsStaffs',                  UmsStaffs);
    app.component('UmsStaffsLatest',            UmsStaffsLatest);
    app.component('UmsUsers',                   UmsUsers);

        app.component('UmsDetailBioData',               UmsDetailBioData);
        app.component('UmsDetailUserlist',              UmsDetailUserlist);

        app.component('UmsFormAssignRole',              UmsFormAssignRole);
        app.component('UmsFormBioData',                 UmsFormBioData);
        app.component('UmsFormNOK',                     UmsFormNOK);
        app.component('UmsFormPassword',                UmsFormPassword);
        app.component('UmsFormRole',                    UmsFormRole);
        app.component('UmsFormStaff',                   UmsFormStaff);
}

export default router;