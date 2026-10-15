import UIKit

private enum DefaultsKey {
    static let studentName = "studentName"
    static let notificationsEnabled = "notificationsEnabled"
    static let selectedRoleIndex = "selectedRoleIndex"
}

class ViewController: UIViewController {
    @IBOutlet private weak var titleLabel: UILabel!
    @IBOutlet private weak var subtitleLabel: UILabel!
    @IBOutlet private weak var nameTextField: UITextField!
    @IBOutlet private weak var notifySwitch: UISwitch!
    @IBOutlet private weak var roleSegmentedControl: UISegmentedControl!
    @IBOutlet private weak var eventDatePicker: UIDatePicker!
    @IBOutlet private weak var guestCountLabel: UILabel!
    @IBOutlet private weak var guestStepper: UIStepper!

    override func viewDidLoad() {
        super.viewDidLoad()
        title = "Campus Companion"
        titleLabel.text = "Campus Companion"
        updateGuestCountLabel()
        nameTextField.text = UserDefaults.standard.string(forKey: DefaultsKey.studentName) ?? ""
        notifySwitch.isOn = UserDefaults.standard.bool(forKey: DefaultsKey.notificationsEnabled)
        roleSegmentedControl.selectedSegmentIndex = UserDefaults.standard.integer(
            forKey: DefaultsKey.selectedRoleIndex
        )
    }

    @IBAction private func getStartedTapped(_ sender: UIButton) {
        subtitleLabel.text = "Let's get started!"
    }

    @IBAction private func guestStepperChanged(_ sender: UIStepper) {
        updateGuestCountLabel()
    }

    @IBAction private func exploreButtonTapped(_ sender: UIButton) {
        nameTextField.resignFirstResponder()
        UserDefaults.standard.set(nameTextField.text ?? "", forKey: DefaultsKey.studentName)
        UserDefaults.standard.set(notifySwitch.isOn, forKey: DefaultsKey.notificationsEnabled)
        UserDefaults.standard.set(
            roleSegmentedControl.selectedSegmentIndex,
            forKey: DefaultsKey.selectedRoleIndex
        )
        performSegue(withIdentifier: "ShowDetailSegue", sender: self)
    }

    @IBAction private func resetPreferencesTapped(_ sender: UIButton) {
        nameTextField.resignFirstResponder()
        UserDefaults.standard.removeObject(forKey: DefaultsKey.studentName)
        UserDefaults.standard.removeObject(forKey: DefaultsKey.notificationsEnabled)
        UserDefaults.standard.removeObject(forKey: DefaultsKey.selectedRoleIndex)
        nameTextField.text = ""
        notifySwitch.isOn = false
        roleSegmentedControl.selectedSegmentIndex = 0
    }

    override func prepare(for segue: UIStoryboardSegue, sender: Any?) {
        guard segue.identifier == "ShowDetailSegue",
              let destination = segue.destination as? DetailViewController else {
            return
        }

        let enteredName = (nameTextField.text ?? "")
            .trimmingCharacters(in: .whitespacesAndNewlines)

        destination.studentName = enteredName.isEmpty ? "Student" : enteredName
        destination.notificationsEnabled = notifySwitch.isOn
        destination.selectedRole = roleSegmentedControl.selectedSegmentIndex == 1
            ? "Faculty"
            : "Student"
        destination.preferredEventDate = eventDatePicker.date
        destination.guestCount = max(1, Int(guestStepper.value))
    }

    private func updateGuestCountLabel() {
        guestCountLabel.text = "Number of Guests: \(max(1, Int(guestStepper.value)))"
    }
}
