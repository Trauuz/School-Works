import UIKit

class DetailViewController: UIViewController {
    @IBOutlet private weak var messageLabel: UILabel!

    var studentName: String = "Student"
    var notificationsEnabled: Bool = false
    var selectedRole: String = "Student"
    var preferredEventDate: Date = Date()
    var guestCount: Int = 1
    var announcement: CampusAnnouncement?

    override func viewDidLoad() {
        super.viewDidLoad()

        if let announcement {
            title = announcement.category
            messageLabel.text = """
            \(announcement.title ?? "")
            Category: \(announcement.category ?? "")
            Date: \(announcement.date ?? "")
            Location: \(announcement.location ?? "Not specified")
            Priority Level: \(announcement.priority ?? "Normal")
            Posted By: \(announcement.postedBy)
            """
            return
        }

        title = "Campus Events"

        let notificationStatus = notificationsEnabled ? "on" : "off"
        let dateFormatter = DateFormatter()
        dateFormatter.dateStyle = .medium
        dateFormatter.timeStyle = .none

        messageLabel.text = """
        Welcome, \(studentName)! (\(selectedRole))
        Notifications: \(notificationStatus).
        Event: \(dateFormatter.string(from: preferredEventDate))
        Guests: \(guestCount)
        """
    }
}
