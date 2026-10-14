import UIKit

final class ViewController: UIViewController {
    @IBOutlet private weak var titleLabel: UILabel!
    @IBOutlet private weak var subtitleLabel: UILabel!
    @IBOutlet private weak var campusImageView: UIImageView!
    @IBOutlet private weak var nameTextField: UITextField!
    @IBOutlet private weak var notifySwitch: UISwitch!
    @IBOutlet private weak var roleSegmentedControl: UISegmentedControl!
    @IBOutlet private weak var eventDatePicker: UIDatePicker!
    @IBOutlet private weak var guestsStepper: UIStepper!
    @IBOutlet private weak var guestsLabel: UILabel!

    override func viewDidLoad() {
        super.viewDidLoad()
        title = "Campus Companion"
        titleLabel.text = "Campus Companion"
        subtitleLabel.text = "Your Campus, In Your Pocket"
        campusImageView.image = UIImage(systemName: "building.columns.circle.fill")
        eventDatePicker.datePickerMode = .date
        eventDatePicker.minimumDate = Calendar.current.startOfDay(for: Date())
        updateGuestsLabel()
    }

    @IBAction private func getStartedTapped(_ sender: UIButton) {
        // Keep the original text-change feature before opening the welcome screen.
        subtitleLabel.text = "Let's get started!"
        DispatchQueue.main.asyncAfter(deadline: .now() + 0.35) { [weak self] in
            self?.performSegue(withIdentifier: "ShowDetailSegue", sender: self)
        }
    }
    @IBAction private func guestsStepperChanged(_ sender: UIStepper) { updateGuestsLabel() }

    @IBAction private func exploreButtonTapped(_ sender: UIButton) {
        nameTextField.resignFirstResponder()
        performSegue(withIdentifier: "ShowDetailSegue", sender: self)
    }

    override func prepare(for segue: UIStoryboardSegue, sender: Any?) {
        guard segue.identifier == "ShowDetailSegue", let detailViewController = segue.destination as? DetailViewController else { return }
        let enteredName = nameTextField.text?.trimmingCharacters(in: .whitespacesAndNewlines) ?? ""
        detailViewController.studentName = enteredName.isEmpty ? "Student" : enteredName
        detailViewController.notificationsEnabled = notifySwitch.isOn
        detailViewController.selectedRole = roleSegmentedControl.selectedSegmentIndex == 0 ? "Student" : "Faculty"
        detailViewController.preferredEventDate = eventDatePicker.date
        detailViewController.numberOfGuests = Int(guestsStepper.value)
    }

    private func updateGuestsLabel() { guestsLabel.text = "Guests: \(Int(guestsStepper.value))" }
}
